import time
import requests
import cv2
from gpiozero import DistanceSensor, LED, Servo

# === CONFIGURAÇÕES ===
SERVER_URL = "https://iot.dei.estg.ipleiria.pt/ti/ti007/ti/api/api.php"
CMD_URL = f"{SERVER_URL}?get=1"
WEBCAM_URL = "http://192.168.137.98:4747/video"

# === INICIALIZAÇÃO HARDWARE ===
sensor = DistanceSensor(echo=20, trigger=21, max_distance=2.0)
led_red = LED(5)
led_green = LED(6)
led_yellow = LED(13)
servo_gate = Servo(12)

# === PARÂMETROS GLOBAIS ===
GATE_TIME = 0.2
IMAGE_INTERVAL = 6
SEND_INTERVAL = 30  # segundos entre envios mesmo sem mudança

# === VARIÁVEIS DE CONTROLO ===
current_gate_position = None
last_gate_state = None
last_image_time = 0
last_distance = 100  # Inicializa com valor válido
last_led_value = None
manual_override_until = 0
manual_led_override_until = 0
MANUAL_OVERRIDE_DURATION = 10

last_send_times = {
    "distancia": 0,
    "cancela": 0,
    "led": 0,
}

# === FUNÇÕES ===
def read_distance():
    try:
        distance_m = sensor.distance
        distance_cm = round(distance_m * 100, 2)
        if 0 <= distance_cm <= 200:
            return distance_cm
        else:
            raise ValueError("Distância fora do intervalo.")
    except Exception:
        return last_distance if last_distance else 100  # fallback

def control_gate(distance_cm):
    global current_gate_position

    if distance_cm <= 10:
        if current_gate_position != "open":
            servo_gate.max()
            time.sleep(GATE_TIME)
            servo_gate.detach()
            current_gate_position = "open"
            return 1
    else:
        if current_gate_position != "closed":
            servo_gate.min()
            time.sleep(GATE_TIME)
            servo_gate.detach()
            current_gate_position = "closed"
            return -1

    return 0

def gate_closed():
    global current_gate_position
    servo_gate.min()
    time.sleep(GATE_TIME)
    servo_gate.detach()
    current_gate_position = "closed"
    return -1

def gate_opened():
    global current_gate_position
    servo_gate.max()
    time.sleep(GATE_TIME)
    servo_gate.detach()
    current_gate_position = "open"
    return 1

def control_led(humidity):
    reset_leds()

    if humidity >= 80.0 or humidity <= 29.0:
        led_red.on()
        return 3  # Crítica
    elif 65.0 <= humidity < 80.0 or 30.0 <= humidity < 46.0:
        led_yellow.on()
        return 2  # Alerta
    elif 46.0 <= humidity < 65.0:
        led_green.on()
        return 1  # OK
    else:
        return 0  # Não definido / fora do previsto
    
def reset_leds():
    led_red.off()
    led_green.off()
    led_yellow.off()
    return 0

def send_data(name, value):
    max_retries = 3
    retry_delay = 1  # segundo
    for attempt in range(max_retries):
        try:
            response = requests.post(SERVER_URL, data={'nome': name, 'valor': value}, timeout=5)
            if response.status_code == 200:
                print(f"[ENVIADO] {name} = {value}")
                last_send_times[name] = time.time()
                return True
            else:
                print(f"[ERRO] ao enviar {name} — Código HTTP: {response.status_code}")
        except Exception as e:
            print(f"[EXCEÇÃO] ao enviar {name} (tentativa {attempt+1}): {e}")
        time.sleep(retry_delay)
    print(f"[FALHA] não foi possível enviar {name} após {max_retries} tentativas.")
    return False

def get_commands():
    try:
        response = requests.get(CMD_URL, timeout=5)
        if response.status_code == 200:
            return response.text.strip().splitlines()
        else:
            print(f"[ERRO] ao obter comandos — Código HTTP: {response.status_code}")
            return []
    except Exception as e:
        print(f"[EXCEÇÃO] ao obter comandos: {e}")
        return []

def parse_command(lines, target):
    for line in lines:
        parts = line.strip().split(';')
        if len(parts) == 2 and parts[0].strip() == target:
            try:
                return float(parts[1])
            except ValueError:
                return None
    return None

def capture_image():
    cap = cv2.VideoCapture(WEBCAM_URL)
    try:
        if not cap.isOpened():
            print("[ERRO] Webcam não disponível.")
            return False

        ret, frame = cap.read()
        if not ret:
            print("[ERRO] Não foi possível ler frame da webcam.")
            return False

        filename = 'captura.jpg'
        cv2.imwrite(filename, frame)

        with open(filename, 'rb') as img_file:
            response = requests.post(SERVER_URL, data={'nome': 'webcam'}, files={'imagem': img_file}, timeout=5)

        return response.status_code == 200

    except Exception as e:
        print("[EXCEÇÃO] ao capturar imagem:", e)
        return False

    finally:
        cap.release()

def main():
    global last_gate_state, last_image_time, last_distance, last_led_value
    global current_gate_position, manual_override_until, manual_led_override_until

    # Inicializar variáveis de controlo de erros
    send_fail_count = 0
    get_commands_fail_count = 0
    MAX_FAILS = 5  # número de falhas consecutivas para disparar alerta/reset

    distance = read_distance()
    control_gate(distance)
    last_gate_state = distance <= 10
    last_distance = distance

    if last_led_value is None:
        last_led_value = 0
    if "led" not in last_send_times:
        last_send_times["led"] = 0

    while True:
        now = time.time()

        # === Obter comandos ===
        commands = []
        try:
            commands = get_commands()
            get_commands_fail_count = 0  # reset contador se sucesso
        except Exception as e:
            get_commands_fail_count += 1
            print(f"[ERRO] Falha a obter comandos: {e}")
            if get_commands_fail_count >= MAX_FAILS:
                print("[ALERTA] Falha repetida a obter comandos, reiniciar processo ou alertar.")
                # Aqui podes colocar reset do sistema ou outro mecanismo
                get_commands_fail_count = 0  # reset para evitar loop infinito

        # === Ler distância só se não houver controlo manual da cancela ===
        if now > manual_override_until:
            distance = read_distance()
        current_gate_state = distance <= 10

        # === CONTROLO DA CANCELA ===
        if now > manual_override_until:
            print("[INFO] Controlo automático da cancela ativo.")
            new_state = current_gate_state
            if new_state != last_gate_state or now - last_send_times["cancela"] > SEND_INTERVAL:
                angle = control_gate(distance)
                if angle != 0 or now - last_send_times["cancela"] > SEND_INTERVAL:
                    if send_data("cancela", angle if angle != 0 else (1 if new_state else -1)):
                        last_gate_state = new_state
                        send_fail_count = 0
                    else:
                        send_fail_count += 1
        else:
            print("[INFO] Controlo manual da cancela ativo, ignorando distância.")
            cancela_cmd = parse_command(commands, "cancela")
            if cancela_cmd is not None and cancela_cmd != 0:
                desired_state = "open" if cancela_cmd == 1 else "closed" if cancela_cmd == -1 else None
                if desired_state and current_gate_position != desired_state:
                    if desired_state == "open":
                        gate_opened()
                    else:
                        gate_closed()
                    time.sleep(GATE_TIME)
                    current_gate_position = desired_state
                    last_gate_state = (distance <= 10)
                    manual_override_until = now + MANUAL_OVERRIDE_DURATION
                    if not send_data("cancela", cancela_cmd):
                        send_fail_count += 1
                else:
                    print(f"[INFO] Cancela já está {desired_state} — não faz nada.")
            else:
                print("[INFO] Sem comando manual da cancela ou comando = 0")

        # Envia distância se mudou ou passou intervalo
        if distance != last_distance or now - last_send_times["distancia"] > SEND_INTERVAL:
            if send_data("distancia", distance):
                last_distance = distance
                send_fail_count = 0
            else:
                send_fail_count += 1

        # === CONTROLO DO LED ===
        led_cmd = parse_command(commands, "led")

        if led_cmd is not None and led_cmd in [0, 1, 2, 3]:
            reset_leds()
            if led_cmd == 1:
                led_green.on()
            elif led_cmd == 2:
                led_yellow.on()
            elif led_cmd == 3:
                led_red.on()
            last_led_value = led_cmd
            if send_data("led", led_cmd):
                send_fail_count = 0
            else:
                send_fail_count += 1
            manual_led_override_until = now + MANUAL_OVERRIDE_DURATION
            last_send_times["led"] = now

        else:
            if now > manual_led_override_until:
                humidity = parse_command(commands, "humidade")
                if humidity is None:
                    led_value = 0
                    reset_leds()
                else:
                    led_value = control_led(humidity)

                if (led_value != last_led_value) or (now - last_send_times["led"] > SEND_INTERVAL):
                    if send_data("led", led_value):
                        last_led_value = led_value
                        last_send_times["led"] = now
                        send_fail_count = 0
                    else:
                        send_fail_count += 1

        # Se as falhas de envio forem muitas, faz reset ou alerta
        if send_fail_count >= MAX_FAILS:
            print("[ALERTA] Muitas falhas consecutivas a enviar dados! Avaliar reinício ou alertar.")
            send_fail_count = 0
            # Exemplo: podes colocar reboot do sistema, reset das variáveis ou notificação

        # === CAPTURA DE IMAGEM ===
        if "imagem" in commands:
            if now - last_image_time > IMAGE_INTERVAL:
                if capture_image():
                    print("[INFO] Imagem capturada.")
                else:
                    print("[ERRO] Falha ao capturar imagem.")
                last_image_time = now
        elif "imagem" not in commands and last_image_time == 0:
            last_image_time = now
        elif "imagem" not in commands and last_image_time > 0:
            if now - last_image_time > IMAGE_INTERVAL:
                if capture_image():
                    print("[INFO] Imagem capturada.")
                else:
                    print("[ERRO] Falha ao capturar imagem.")
                last_image_time = now
        else:
            print("[INFO] Sem comando de captura de imagem — não faz nada.")

        print(f"[DEBUG] Distância: {distance} cm | Cancela: {'Aberta' if last_gate_state else 'Fechada'} | LED: {last_led_value}")

        time.sleep(1)


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("Execução interrompida pelo utilizador.")