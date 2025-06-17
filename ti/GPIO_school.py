import RPi.GPIO as GPIO
import requests
import time

# === CONFIGURAÇÕES ===
SERVER_URL = "https://iot.dei.estg.ipleiria.pt/ti/ti007/ti/api/api.php"
CMD_URL = f"{SERVER_URL}?get=1"
NOME_CANCELA = "cancela"
NOME_LED = "led"

TRIG = 17
ECHO = 27
SERVO_PIN = 18
LED_R = 22
LED_G = 23
LED_B = 24

GPIO.setmode(GPIO.BCM)
GPIO.setup(TRIG, GPIO.OUT)
GPIO.setup(ECHO, GPIO.IN)
GPIO.setup(SERVO_PIN, GPIO.OUT)
GPIO.setup(LED_R, GPIO.OUT)
GPIO.setup(LED_G, GPIO.OUT)
GPIO.setup(LED_B, GPIO.OUT)

servo = GPIO.PWM(SERVO_PIN, 50)  # 50 Hz
servo.start(0)

# === FUNÇÕES ===

def read_distance():
    GPIO.output(TRIG, False)
    time.sleep(0.05)
    GPIO.output(TRIG, True)
    time.sleep(0.00001)
    GPIO.output(TRIG, False)

    while GPIO.input(ECHO) == 0:
        pulse_start = time.time()

    while GPIO.input(ECHO) == 1:
        pulse_end = time.time()

    pulse_duration = pulse_end - pulse_start
    distance = round(pulse_duration * 17150, 2)  # cm
    return distance

def control_gate(distance):
    angle = 50 if distance < 10 else 160
    duty = angle / 18 + 2.5
    servo.ChangeDutyCycle(duty)
    return angle

def control_led(color):
    # 1 = verde, 2 = amarelo, 3 = vermelho
    if color == 1:
        GPIO.output(LED_R, False)
        GPIO.output(LED_G, True)
        GPIO.output(LED_B, False)
    elif color == 2:
        GPIO.output(LED_R, True)
        GPIO.output(LED_G, True)
        GPIO.output(LED_B, False)
    elif color == 3:
        GPIO.output(LED_R, True)
        GPIO.output(LED_G, False)
        GPIO.output(LED_B, False)
    else:
        GPIO.output(LED_R, False)
        GPIO.output(LED_G, False)
        GPIO.output(LED_B, False)

def send_data(nome, valor):
    try:
        r = requests.post(SERVER_URL, data={'nome': nome, 'valor': valor})
        print(f"POST {nome}={valor} -> {r.status_code}")
    except Exception as e:
        print(f"Erro ao enviar {nome}: {e}")

def get_commands():
    try:
        r = requests.get(CMD_URL)
        if r.status_code == 200:
            return r.text.strip().splitlines()
        return []
    except:
        return []

def parse_command(lines, target):
    for line in lines:
        if target in line:
            try:
                return int(line.split(';')[1])
            except:
                return None
    return None

# === LOOP PRINCIPAL ===
try:
    while True:
        distance = read_distance()
        angle = control_gate(distance)
        send_data("distancia", distance)
        send_data("cancela", angle)

        commands = get_commands()
        led_value = parse_command(commands, NOME_LED)
        if led_value:
            control_led(led_value)

        time.sleep(1)

except KeyboardInterrupt:
    print("A terminar...")
finally:
    servo.stop()
    GPIO.cleanup()
