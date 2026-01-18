# Estrutura do Projeto - Estufa Inteligente (Smart Greenhouse)

## Índice
1. [Visão Geral](#visão-geral)
2. [Arquitetura do Sistema](#arquitetura-do-sistema)
3. [Estrutura de Diretórios](#estrutura-de-diretórios)
4. [Como Funciona](#como-funciona)
5. [Como Adicionar Novos Sensores/Atuadores](#como-adicionar-novos-sensoresatuadores)
6. [API e Endpoints](#api-e-endpoints)
7. [Integração RPI e Arduino](#integração-rpi-e-arduino)
8. [Páginas Web Disponíveis](#páginas-web-disponíveis)

---

## Visão Geral

Este é um projeto IoT de **Estufa Inteligente** desenvolvido no âmbito da disciplina de Tecnologias de Internet. O sistema permite:
- Monitorização de temperatura e humidade em tempo real
- Controlo remoto de atuadores (LEDs, ventiladores, etc.)
- Captura de imagens através de webcam
- Visualização de histórico e gráficos
- Sistema de autenticação e permissões de utilizadores

## Arquitetura do Sistema

O sistema é composto por **3 componentes principais**:

```
┌─────────────────┐      HTTP/API      ┌─────────────────┐
│   Arduino       │◄────────────────────┤   Servidor Web  │
│   (Sensores)    │─────────────────────►│   (PHP)         │
└─────────────────┘                     └─────────────────┘
                                               ▲
┌─────────────────┐      HTTP/API             │
│   Raspberry Pi  │──────────────────────────►│
│   (Atuadores)   │◄──────────────────────────┘
└─────────────────┘
```

1. **Arduino**: Recolhe dados de sensores (temperatura, humidade) e controla LEDs
2. **Raspberry Pi**: Controla atuadores, captura imagens da webcam, monitoriza sensores
3. **Servidor Web (PHP)**: Armazena dados, serve interface web, gere API REST

## Estrutura de Diretórios

```
TI-Project/
│
├── index.php                 # Página de login
├── dashboard.php             # Dashboard principal com sensores/atuadores
├── grafico.php               # Visualização de gráficos
├── historico.php             # Histórico de dados dos sensores
├── historicoimages.php       # Histórico de imagens capturadas
├── clima.php                 # Dados de clima
├── theme.php                 # Gestão de tema (dark/light)
├── logout.php                # Logout do utilizador
├── usarCamera.php            # Controlo da captura de imagens
│
├── api/                      # Backend API
│   ├── api.php              # Endpoint principal (GET/POST)
│   ├── Accounts.php         # Gestão de contas
│   ├── upload.php           # Upload de imagens
│   └── files/               # Armazenamento de dados
│       ├── temperatura/     # Dados do sensor de temperatura
│       │   ├── valor.txt    # Valor atual
│       │   ├── hora.txt     # Timestamp da última leitura
│       │   ├── nome.txt     # Nome do sensor
│       │   └── log.txt      # Histórico (hora;valor)
│       ├── humidade/        # Dados do sensor de humidade
│       ├── atuadorNo1/      # Dados do atuador 1
│       ├── atuadorNo2/      # Dados do atuador 2
│       ├── sensorNo3/       # Dados do sensor 3
│       ├── ledArduino1/     # Estado do LED Arduino 1
│       ├── ledArduino2/     # Estado do LED Arduino 2
│       ├── accounts/        # Contas de utilizadores
│       ├── theme/           # Configuração de tema
│       ├── images/          # Imagens capturadas
│       └── historicoImages/ # Histórico de imagens
│
├── RPI_ARDUINO/             # Código para dispositivos IoT
│   ├── RPI.py              # Script Python para Raspberry Pi
│   └── arduino_get_and_post.ino  # Código Arduino
│
├── scripts/                 # JavaScript
│   ├── scriptHTTP.js       # Funções HTTP/AJAX
│   └── scriptTheme.js      # Gestão de tema
│
├── style/                   # CSS
│   ├── style.css           # Estilos gerais
│   └── style-login.css     # Estilos da página de login
│
├── imgs/                    # Imagens estáticas
└── README.md               # Documentação básica
```

## Como Funciona

### 1. Fluxo de Dados dos Sensores

```
Arduino/RPI → POST /api/api.php → Armazena em /api/files/{nome}/ → Dashboard exibe
```

**Exemplo**: Arduino lê temperatura de 25.5°C
1. Arduino faz POST: `nome=temperatura&valor=25.5&hora=2024-01-18 10:30:00`
2. API grava em:
   - `api/files/temperatura/valor.txt` → `25.5`
   - `api/files/temperatura/hora.txt` → `2024-01-18 10:30:00`
   - `api/files/temperatura/log.txt` → `2024-01-18 10:30:00;25.5` (append)
3. Dashboard lê e exibe o valor

### 2. Fluxo de Controlo de Atuadores

```
Dashboard → POST /api/api.php → Atualiza ficheiro → Arduino/RPI lê → Ativa atuador
```

**Exemplo**: Utilizador liga o LED Arduino 2
1. Utilizador clica no botão no dashboard
2. Dashboard faz POST: `nome=ledArduino2&valor=Ligado&hora=...`
3. API atualiza `api/files/ledArduino2/valor.txt` → `Ligado`
4. Arduino faz GET: `/api/api.php?nome=ledArduino2`
5. Arduino recebe "Ligado" e liga o LED

### 3. Sistema de Autenticação

- Credenciais armazenadas em `api/files/accounts/accounts.txt` (formato: `username;password_hash`)
- Permissões em `api/files/accounts/accountsSettings.txt` (formato: `username;permission`)
- Sessões PHP para manter utilizador autenticado

## Como Adicionar Novos Sensores/Atuadores

### Passo 1: Criar a Estrutura de Ficheiros

Para adicionar um novo sensor/atuador chamado `sensorNovo`, crie a estrutura:

```bash
mkdir api/files/sensorNovo
touch api/files/sensorNovo/valor.txt
touch api/files/sensorNovo/hora.txt
touch api/files/sensorNovo/nome.txt
touch api/files/sensorNovo/log.txt
```

**Conteúdo inicial:**
- `nome.txt`: Nome do sensor (ex: "Sensor de Luz")
- `valor.txt`: Valor inicial (ex: "0")
- `hora.txt`: Timestamp inicial (ex: "2024-01-18 00:00:00")
- `log.txt`: Vazio (será preenchido automaticamente)

### Passo 2: Adicionar no Código do Dispositivo IoT

#### Para Arduino (arduino_get_and_post.ino):

**Enviar dados (POST):**
```cpp
// Exemplo: Enviar valor do novo sensor
float valorSensor = lerSensorNovo(); // Sua função para ler o sensor
char datahora[20];
update_time(datahora);
post2API("sensorNovo", String(valorSensor), datahora);
```

**Receber comandos (GET):**
```cpp
// Exemplo: Receber estado de um atuador
clienteHTTP.get("/projeto/api/api.php?nome=sensorNovo");
delay(1000);
int response = clienteHTTP.responseStatusCode();
if (response == 200) {
    String HTTP = clienteHTTP.responseBody();
    // Processar resposta
    if (HTTP.equals("Ligado")) {
        // Ligar atuador
    }
}
```

#### Para Raspberry Pi (RPI.py):

**Enviar dados (POST):**
```python
import requests
import time

def enviar_sensor_novo():
    API_URL = 'http://10.20.228.90/projeto/api/api.php'
    valor = ler_sensor_novo()  # Sua função para ler o sensor
    payload = {
        'nome': 'sensorNovo',
        'valor': str(valor),
        'hora': time.strftime('%Y-%m-%d %H:%M:%S')
    }
    requests.post(API_URL, data=payload)
```

**Receber comandos (GET):**
```python
def controlar_atuador_novo():
    URL = "http://10.20.228.90/projeto/api/api.php?nome=sensorNovo"
    try:
        resposta = requests.get(URL)
        if resposta.status_code == 200:
            estado = resposta.text
            if estado == "Ligado":
                # Ligar atuador
                pass
            else:
                # Desligar atuador
                pass
    except Exception as e:
        print("Erro:", e)
```

### Passo 3: Adicionar na Interface Web (dashboard.php)

Localize a secção onde os sensores/atuadores são exibidos e adicione:

```php
<?php
    // Ler dados do novo sensor
    $valor_sensorNovo = file_get_contents("api/files/sensorNovo/valor.txt");
    $hora_sensorNovo = file_get_contents("api/files/sensorNovo/hora.txt");
    $nome_sensorNovo = file_get_contents("api/files/sensorNovo/nome.txt");
?>

<!-- HTML para exibir o sensor -->
<div class="card">
    <h3><?php echo $nome_sensorNovo; ?></h3>
    <p>Valor: <?php echo $valor_sensorNovo; ?></p>
    <p>Última atualização: <?php echo $hora_sensorNovo; ?></p>
</div>
```

Para atuadores com controlo:
```html
<!-- Botão para controlar atuador -->
<button onclick="controlarAtuador('sensorNovo', 'Ligado')">Ligar</button>
<button onclick="controlarAtuador('sensorNovo', 'Desligado')">Desligar</button>
```

### Passo 4: Adicionar no Gráfico (grafico.php)

Adicione o nome do novo sensor/atuador no array:

```php
$nomesSensoresAtuadores = array(
    'temperatura',
    'humidade',
    'sensorNo3',
    'atuadorNo1',
    'atuadorNo2',
    'ledArduino1',
    'ledArduino2',
    'sensorNovo'  // Adicione aqui
);
```

### Passo 5: Testar

1. **Teste a API manualmente:**
   ```bash
   # POST - Enviar dados
   curl -X POST http://seu-servidor/projeto/api/api.php \
        -d "nome=sensorNovo&valor=100&hora=2024-01-18 10:00:00"
   
   # GET - Ler dados
   curl http://seu-servidor/projeto/api/api.php?nome=sensorNovo
   ```

2. **Verifique os ficheiros:**
   ```bash
   cat api/files/sensorNovo/valor.txt
   cat api/files/sensorNovo/log.txt
   ```

3. **Teste no dashboard:**
   - Faça login
   - Verifique se o sensor aparece
   - Teste controlo (se for atuador)

## API e Endpoints

### Endpoint Principal: `/api/api.php`

#### POST - Enviar Dados
**Uso**: Enviar dados de sensores ou comandos para atuadores

**Parâmetros:**
- `nome` (obrigatório): Nome do sensor/atuador
- `valor` (obrigatório): Valor a armazenar
- `hora` (obrigatório): Timestamp no formato `YYYY-MM-DD HH:MM:SS`

**Exemplo:**
```bash
POST /api/api.php
Content-Type: application/x-www-form-urlencoded

nome=temperatura&valor=25.5&hora=2024-01-18 10:30:00
```

**Respostas:**
- `200 OK`: Dados armazenados com sucesso
- `404 Not Found`: Sensor/atuador não existe
- `400 Bad Request`: Parâmetros faltando

#### GET - Ler Dados
**Uso**: Obter o valor atual de um sensor/atuador

**Parâmetros:**
- `nome` (obrigatório): Nome do sensor/atuador

**Exemplo:**
```bash
GET /api/api.php?nome=temperatura
```

**Resposta:**
```
25.5
```

### Endpoint de Upload: `/api/upload.php`

**Uso**: Upload de imagens da webcam

**Método**: POST com multipart/form-data

**Exemplo:**
```python
files = {'file': open('webcam.jpg', 'rb'), 'hora': open('hora.txt', 'rb')}
response = requests.post('http://servidor/api/upload.php', files=files)
```

## Integração RPI e Arduino

### Arduino (arduino_get_and_post.ino)

**Funções principais:**
- `setup()`: Inicializa WiFi, sensores e pinos
- `loop()`: Loop principal que executa a cada intervalo
- `post2API(nome, valor, hora)`: Envia dados via POST
- `update_time(datahora)`: Obtém timestamp do servidor NTP

**Configuração WiFi:**
```cpp
char SSID[] = "labs";
char PASS_WIFI[] = "1nv3nt@r2023_IPLEIRIA";
char URL[] = "10.20.228.90";
```

**Sensores suportados:**
- DHT11: Temperatura e humidade
- Botão digital
- LED de output

### Raspberry Pi (RPI.py)

**Funções principais:**
- `controla_temperatura()`: Controla atuador baseado na temperatura
- `controla_atuador2()`: Controla atuador via API
- `captura_imagem()`: Captura e envia imagem da webcam
- `controle_botao()`: Lê estado do botão físico

**Configuração de pinos GPIO:**
```python
LED_PIN_TEMPERATURA = 2
LED_PIN_ATUADOR2 = 3
BUTTON_PIN = 26
```

**URLs configuráveis:**
```python
URL_TEMPERATURA = "http://10.20.228.90/projeto/api/api.php?nome=temperatura"
SERVER_URL = 'http://10.20.228.90/projeto/api/upload.php'
```

## Páginas Web Disponíveis

### index.php
- **Função**: Página de login
- **Acesso**: Público
- **Autenticação**: Verifica credenciais em `api/files/accounts/accounts.txt`

### dashboard.php
- **Função**: Dashboard principal com todos os sensores e atuadores
- **Acesso**: Requer autenticação
- **Funcionalidades**:
  - Visualização em tempo real dos sensores
  - Controlo de atuadores
  - Auto-refresh a cada 60 segundos

### grafico.php
- **Função**: Visualização de gráficos históricos
- **Acesso**: Requer autenticação (root)
- **Parâmetro GET**: `?nome=temperatura` (ou outro sensor)
- **Funcionalidades**:
  - Gráficos de linha temporal
  - Histórico completo do log.txt

### historico.php
- **Função**: Listagem de histórico de dados
- **Acesso**: Requer autenticação
- **Funcionalidades**:
  - Tabela com todos os valores históricos
  - Filtro por sensor/atuador

### historicoimages.php
- **Função**: Galeria de imagens capturadas
- **Acesso**: Requer autenticação
- **Funcionalidades**:
  - Visualização de imagens da webcam
  - Timestamp de cada captura

### clima.php
- **Função**: Informações de clima
- **Acesso**: Requer autenticação

### theme.php
- **Função**: Alternar entre tema claro e escuro
- **Acesso**: Requer autenticação

### usarCamera.php
- **Função**: Controlo de captura de imagem
- **Acesso**: Requer autenticação
- **Funcionalidades**:
  - Botão para acionar captura
  - Envia impulso para o Raspberry Pi

## Dicas e Boas Práticas

### 1. Nomenclatura
- Use nomes descritivos sem espaços: `temperaturaExterna`, `sensorLuz`, `atuadorVentilador`
- Mantenha consistência com os nomes existentes

### 2. Estrutura de Ficheiros
- **SEMPRE** crie os 4 ficheiros: `valor.txt`, `hora.txt`, `nome.txt`, `log.txt`
- O ficheiro `log.txt` usa o formato: `hora;valor` (uma linha por leitura)

### 3. Formato de Dados
- Datas: `YYYY-MM-DD HH:MM:SS` (ex: `2024-01-18 15:30:00`)
- Estados de atuadores: `Ligado` ou `Desligado` (com maiúscula inicial)
- Valores numéricos: Use ponto como separador decimal (ex: `25.5`)

### 4. Segurança
- Passwords são armazenadas com hash usando `password_hash()` do PHP
- Sempre valide permissões antes de permitir acesso a páginas administrativas
- Use sessões PHP para manter estado de autenticação

### 5. Testes
- Teste sempre a API via curl/Postman antes de integrar no Arduino/RPI
- Verifique os ficheiros manualmente após POST
- Monitore o log.txt para ver se os dados estão a ser guardados

### 6. Debugging
- Arduino: Use `Serial.println()` para debug via porta série
- Raspberry Pi: Use `print()` e verifique a consola
- PHP: Use `error_log()` e verifique os logs do Apache/Nginx
- API: Teste os endpoints com curl para isolar problemas

## Exemplo Completo: Adicionar Sensor de Luminosidade

Vamos adicionar um sensor de luminosidade completo:

### 1. Criar estrutura
```bash
mkdir api/files/luminosidade
echo "Sensor de Luminosidade" > api/files/luminosidade/nome.txt
echo "0" > api/files/luminosidade/valor.txt
echo "2024-01-18 00:00:00" > api/files/luminosidade/hora.txt
touch api/files/luminosidade/log.txt
```

### 2. Arduino (adicionar no loop)
```cpp
// Ler sensor de luminosidade (assumindo sensor no pino A0)
int valorLuminosidade = analogRead(A0);
post2API("luminosidade", String(valorLuminosidade), datahora);
```

### 3. Dashboard (adicionar card)
```php
<?php
    $valor_luminosidade = file_get_contents("api/files/luminosidade/valor.txt");
    $hora_luminosidade = file_get_contents("api/files/luminosidade/hora.txt");
    $nome_luminosidade = file_get_contents("api/files/luminosidade/nome.txt");
?>

<div class="sensor-card">
    <h3><?php echo $nome_luminosidade; ?></h3>
    <div class="valor"><?php echo $valor_luminosidade; ?></div>
    <div class="hora"><?php echo $hora_luminosidade; ?></div>
</div>
```

### 4. Adicionar no array de gráficos
```php
// Em grafico.php
$nomesSensoresAtuadores = array(
    'temperatura', 'humidade', 'sensorNo3', 'atuadorNo1', 
    'atuadorNo2', 'ledArduino1', 'ledArduino2', 'luminosidade'
);
```

### 5. Testar
```bash
# POST manual
curl -X POST http://localhost/projeto/api/api.php \
     -d "nome=luminosidade&valor=512&hora=2024-01-18 15:00:00"

# GET manual
curl http://localhost/projeto/api/api.php?nome=luminosidade
# Deve retornar: 512

# Verificar log
cat api/files/luminosidade/log.txt
# Deve conter: 2024-01-18 15:00:00;512
```

## Conclusão

Este projeto está estruturado de forma modular, permitindo fácil expansão:
- **API REST simples**: GET para ler, POST para escrever
- **Sistema de ficheiros**: Cada sensor/atuador tem a sua pasta
- **Interface Web**: PHP para backend, JavaScript para interações
- **IoT Devices**: Arduino e Raspberry Pi comunicam via HTTP

Para adicionar novos sensores/atuadores, siga os **5 passos** descritos acima e teste cada componente individualmente antes de integrar tudo.

---

**Desenvolvido por**: Grupo 03 - Engenharia Informática  
**Disciplina**: Tecnologias de Internet  
**Instituição**: Instituto Politécnico de Leiria
