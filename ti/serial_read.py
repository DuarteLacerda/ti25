import serial
import time
from datetime import datetime

# Liga à porta serial do Arduino
arduino = serial.Serial('/dev/ttyUSB0', 9600, timeout=1)
time.sleep(2)  # Dá tempo ao Arduino para reiniciar

# Função para garantir que o número de linhas no ficheiro de log não ultrapasse 50
def manter_50_linhas(ficheiro):
    with open(ficheiro, "r") as f:
        linhas = f.readlines()

    # Se o número de linhas for maior que 50, removemos as linhas mais antigas
    if len(linhas) > 50:
        with open(ficheiro, "w") as f:
            f.writelines(linhas[-50:])  # Mantém as últimas 50 linhas

# Função para escrever no ficheiro de valor (valor.txt)
def escrever_no_valor(ficheiro, valor):
    with open(ficheiro, "w") as f:
        f.write(f"{valor}\n")

# Função para escrever no ficheiro de hora (hora.txt)
def escrever_na_hora(ficheiro, data_hora):
    with open(ficheiro, "w") as f:
        f.write(f"{data_hora}\n")

# Função para escrever no ficheiro de log (log.txt)
def escrever_no_log(ficheiro, data_hora, valor):
    with open(ficheiro, "a") as log_ficheiro:
        log_ficheiro.write(f"{data_hora};{valor}\n")

try:
    while True:
        linha = arduino.readline().decode().strip()
        if linha:
            print("Recebido:", linha)

            # Verifica se a linha contém os dados do ultrassônico e do servo
            if "DIST:" in linha and "ANG:" in linha:
                partes = linha.split()
                distancia = float(partes[0].split(":")[1])
                angulo = int(partes[1].split(":")[1])

                print(f"Distância: {distancia:.2f} cm | Servo: {angulo}°")

                # Gerar a data e hora atual
                now = datetime.now()
                data_hora = now.strftime("%Y/%m/%d %H:%M:%S")

                # Gravar no ficheiro de valor do servo (valor.txt)
                escrever_no_valor("api/servo/valor.txt", angulo)

                # Gravar no ficheiro de valor do ultrassônico (valor.txt)
                escrever_no_valor("api/ultrasonico/valor.txt", f"{distancia:.2f}")

                # Gravar no ficheiro de hora do servo (hora.txt)
                escrever_na_hora("api/servo/hora.txt", data_hora)

                # Gravar no ficheiro de hora do ultrassônico (hora.txt)
                escrever_na_hora("api/ultrasonico/hora.txt", data_hora)

                # Gravar no ficheiro de log do servo (log.txt)
                escrever_no_log("api/servo/log.txt", data_hora, angulo)

                # Gravar no ficheiro de log do ultrassônico (log.txt)
                escrever_no_log("api/ultrasonico/log.txt", data_hora, f"{distancia:.2f}")

                # Manter o número de linhas no ficheiro de log abaixo de 50
                manter_50_linhas("api/ultrasonico/log.txt")
                manter_50_linhas("api/servo/log.txt")
                
                

except KeyboardInterrupt:
    print("A sair...")
finally:
    arduino.close()
    print("Porta serial fechada.")