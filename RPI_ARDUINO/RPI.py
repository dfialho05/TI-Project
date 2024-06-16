import requests
import time
import RPi.GPIO as GPIO
import cv2

# Configuração dos pinos
LED_PIN_TEMPERATURA = 2
LED_PIN_ATUADOR2 = 3
BUTTON_PIN = 26

# URLs para requisições
URL_TEMPERATURA = "http://10.20.228.90/projeto/api/api.php?nome=temperatura"
URL_ATUADOR2 = "http://10.20.228.90/projeto/api/api.php?nome=atuadorNo2"
WEBCAM_URL = 'http://10.20.229.191:4747/video'
SERVER_URL = 'http://10.20.228.90/projeto/api/upload.php'
IMPULSO_URL = 'http://10.20.228.90/projeto/api/upload.php?nome=Impulso'
API_URL = 'http://10.20.228.90/projeto/api/api.php'

# Configuração inicial do GPIO
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(LED_PIN_TEMPERATURA, GPIO.OUT)
GPIO.setup(LED_PIN_ATUADOR2, GPIO.OUT)
GPIO.setup(BUTTON_PIN, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

# Função para controlar o atuador 1
def controla_temperatura(): 
    try:
        respostaGet = requests.get(URL_TEMPERATURA)
        if respostaGet.status_code == 200:
            # Se a temperatura estiver entre 30 e 60, desliga o led, senão, liga o led
            temperatura = respostaGet.text
            if 30 < float(temperatura) < 60:
                print("Led atuador1 desligado")
                payload = {'nome': 'atuadorNo1', 'valor': 'Desligado', 'hora': time.strftime('%Y-%m-%d %H:%M:%S')}
                requests.post(API_URL, data=payload)
                GPIO.output(LED_PIN_TEMPERATURA, GPIO.LOW)
            else:
                print("Led atuador1 ligado")
                payload = {'nome': 'atuadorNo1', 'valor': 'Ligado', 'hora': time.strftime('%Y-%m-%d %H:%M:%S')}
                requests.post(API_URL, data=payload)
                GPIO.output(LED_PIN_TEMPERATURA, GPIO.HIGH)
        else:
            print("Erro:", respostaGet.status_code)
    except Exception as exception:
        print("Erro:", exception)

# Função para controlar o atuador 2
def controla_atuador2():
    try:
        respostaGet = requests.get(URL_ATUADOR2)
        if respostaGet.status_code == 200:
            # Se o estado for Desligado, desliga o led, senão, liga o led
            estado = respostaGet.text
            if estado == "Desligado":
                print("Led atuador2 desligado")
                GPIO.output(LED_PIN_ATUADOR2, GPIO.LOW)
            else:
                print("Led atuador2 ligado")
                GPIO.output(LED_PIN_ATUADOR2, GPIO.HIGH)
        else:
            print("Erro:", respostaGet.status_code)
    except Exception as exception:
        print("Erro:", exception)

# Função para capturar imagem da webcam
def captura_imagem():
    try:
        respostaGet = requests.get(IMPULSO_URL)
        if respostaGet.status_code == 200:
            estado = respostaGet.text
            if estado == "1": # Se o impulso for 1, captura a imagem da webcam e envia para a api
                cap = cv2.VideoCapture(WEBCAM_URL)
                ret, frame = cap.read()
                if ret:
                    cv2.imwrite('webcam.jpg', frame)
                    with open("hora.txt", "w") as f:
                        f.write(time.strftime('%Y-%m-%d %H:%M:%S'))
                    with open('webcam.jpg', 'rb') as webcam, open('hora.txt', 'rb') as hora:
                        files = {'file': webcam, 'hora': hora}
                        response = requests.post(SERVER_URL, files=files)
                    if response.status_code == 200:
                        print("Imagem enviada com sucesso!")
                    else:
                        print(f"Falha ao enviar a imagem. Código de status: {response.status_code}")
                else:
                    print("Erro ao capturar a imagem da webcam. A imagem não será enviada.")
                cap.release()
            else:
                print("Nenhum Impulso recebido!")
        else:
            print("Erro:", respostaGet.status_code)
    except Exception as exception:
        print("Erro:", exception)

# Função para controlar o botão
def controle_botao():
    try:
        botao_estado = GPIO.input(BUTTON_PIN)
        if botao_estado == GPIO.HIGH:
            print("botao ligado")
            payload = {'nome': 'sensorNo3', 'valor': '1', 'hora': time.strftime('%Y-%m-%d %H:%M:%S')}
            requests.post(API_URL, data=payload) # Envia um post com o estado do botao para a api
        else:
            print("botao desligado")
            payload = {'nome': 'sensorNo3', 'valor': '0', 'hora': time.strftime('%Y-%m-%d %H:%M:%S')}
            requests.post(API_URL, data=payload) # Envia um post com o estado do botao para a api
    except Exception as exception:
        print("Erro:", exception)

# Loop principal
try:
    while True:
        controla_temperatura()
        controla_atuador2()
        captura_imagem()
        controle_botao()
        time.sleep(5)
except KeyboardInterrupt:
    print('\nO programa foi interrompido pelo utilizador.')
except Exception as e:
    print('Erro inesperado:', e)
finally:
    GPIO.cleanup()
    print('Terminou o programa')
