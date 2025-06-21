import time
import requests
import cv2
from gpiozero import DistanceSensor, LED, Servo

# === CONFIGURAÇÕES ===
SERVER_URL = "http://192.168.1.27/Arduino-Projects/api/api.php"
CMD_URL = f"{SERVER_URL}?get=1"
WEBCAM_URL = "http://192.168.137.214:4747/video"

# === INICIALIZAÇÃO HARDWARE ===
sensor = DistanceSensor(echo=20, trigger=21, max_distance=2.0)
led_red = LED(5)
led_green = LED(6)
led_yellow = LED(13)
servo_gate = Servo(12)

# === PARÂMETROS GLOBAIS ===
GATE_TIME = 0.2
IMAGE_INTERVAL = 6

# === VARIÁVEIS DE CONTROLO ===
current_gate_position = None
last_gate_state = None
last_image_time = 0
last_distance = None
last_led_value = None
manual_override_until = 0
manual_led_override_until = 0
MANUAL_OVERRIDE_DURATION = 10
MANUAL_LED_OVERRIDE_DURATION = 10

def read_distance():
    distance_m = sensor.distance
    return round(distance_m * 100, 2)

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

def control_led(humidity):
    led_red.off()
    led_green.off()
    led_yellow.off()

    if humidity >= 80.0 or humidity <= 29.0:
        led_red.on()
        return 3
    elif 65.0 <= humidity < 80.0:
        led_red.on()
        led_green.on()
        return 2
    elif 46.0 <= humidity < 65.0:
        led_green.on()
        return 1
    elif 30.0 <= humidity < 46.0:
        led_red.on()
        led_green.on()
        return 2
    else:
        return 0

def send_data(name, value):
    try:
        response = requests.post(SERVER_URL, data={'nome': name, 'valor': value})
        if response.status_code == 200:
            print(f"[ENVIADO] {name} = {value}")
        else:
            print(f"[ERRO] ao enviar {name} — Código HTTP: {response.status_code}")
    except Exception as e:
        print(f"[EXCEÇÃO] ao enviar {name}: {e}")

def get_commands():
    try:
        response = requests.get(CMD_URL)
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
            response = requests.post(SERVER_URL, data={'nome': 'webcam'}, files={'imagem': img_file})

        return response.status_code == 200

    except Exception:
        return False

    finally:
        cap.release()

def main():
    global last_gate_state, last_image_time, last_distance, last_led_value
    global current_gate_position, manual_override_until, manual_led_override_until

    # Inicializar last_gate_state e current_gate_position com a distância atual
    distance = read_distance()
    last_gate_state = distance <= 10
    control_gate(distance)  # Garante cancela no estado certo

    while True:
        now = time.time()

        distance = read_distance()
        current_gate_state = distance <= 10

        if now > manual_override_until:
            # Só executa controlo automático se estiver fora do override manual
            new_state = distance <= 10
            if new_state != last_gate_state:
                angle = control_gate(distance)
                if angle != 0:
                    send_data("cancela", angle)
                last_gate_state = new_state
        else:
            print("[INFO] Cancela em modo manual — automático desativado.")

        if distance != last_distance:
            send_data("distancia", distance)
            last_distance = distance

        commands = get_commands()

        # LED Manual
        led_cmd = parse_command(commands, "led")
        if led_cmd is not None:
            if led_cmd != last_led_value or now > manual_led_override_until:
                print(f"[CMD] LED manual = {led_cmd}")
                led_red.off()
                led_green.off()
                led_yellow.off()
                if led_cmd == 1:
                    led_green.on()
                elif led_cmd == 2:
                    led_green.on()
                    led_red.on()
                elif led_cmd == 3:
                    led_red.on()
                led_value = led_cmd
                last_led_value = led_cmd
                send_data("led", led_cmd)
                manual_led_override_until = now + MANUAL_LED_OVERRIDE_DURATION
            else:
                print("[INFO] LED manual já ativo — temporizador não renovado.")
        else:
            if now > manual_led_override_until:
                humidity = parse_command(commands, "humidade")
                if humidity is None:
                    print("[AVISO] Humidade não recebida — LED não será atualizado.")
                    led_value = 0
                    led_red.off()
                    led_green.off()
                    led_yellow.off()
                else:
                    led_value = control_led(humidity)

                if led_value != last_led_value:
                    send_data("led", led_value)
                    last_led_value = led_value
            else:
                print("[INFO] LED em modo manual — automático desativado.")

        # Comando manual da cancela
        cancela_cmd = parse_command(commands, "cancela")
        if cancela_cmd is not None and cancela_cmd != 0:
            desired_state = "open" if cancela_cmd == 1 else "closed" if cancela_cmd == -1 else None
            if desired_state and current_gate_position != desired_state:
                if desired_state == "open":
                    servo_gate.max()
                    print("[CMD] Cancela manual: ABRIR")
                else:
                    servo_gate.min()
                    print("[CMD] Cancela manual: FECHAR")
                time.sleep(GATE_TIME)
                servo_gate.detach()
                current_gate_position = desired_state
                last_gate_state = (distance <= 10)  # evita reversão imediata pelo controlo automático

                # Atualiza override manual
                manual_override_until = now + MANUAL_OVERRIDE_DURATION

                # Envia o estado correto da cancela (1 = aberta, -1 = fechada)
                send_data("cancela", cancela_cmd)
            else:
                print(f"[INFO] Cancela já está {desired_state} — não faz nada.")
        else:
            print("[INFO] Sem comando manual da cancela ou comando = 0")

        # Captura imagem se passou o intervalo
        if now - last_image_time > IMAGE_INTERVAL:
            if capture_image():
                print("[INFO] Imagem capturada.")
            else:
                print("[ERRO] Falha ao capturar imagem.")
            last_image_time = now

        time.sleep(1)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("Execução interrompida pelo utilizador.")