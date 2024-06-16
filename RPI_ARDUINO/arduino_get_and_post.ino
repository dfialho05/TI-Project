#include <WiFi101.h>
#include <ArduinoHttpClient.h>
#include <DHT.h>
#include <NTPClient.h>
#include <WiFiUdp.h> 
#include <TimeLib.h>

// Variaveis de Redes
char SSID[] = "labs";
char PASS_WIFI[] = "1nv3nt@r2023_IPLEIRIA";
char URL[] = "10.20.228.90";
int PORTO = 80;  
WiFiClient clienteWifi;
HttpClient clienteHTTP = HttpClient(clienteWifi, URL, PORTO);



//sensor com post
#define DHTPIN 0           // Pin Digital onde está ligado o sensor
#define DHTTYPE DHT11      // Tipo de sensor DHT
#define ledArduino2 2
#define botao 1
DHT dht(DHTPIN, DHTTYPE);  // Instanciar e declarar a class DHT
WiFiUDP clienteUDP;
char NTP_SERVER[] = "ntp.ipleiria.pt";
NTPClient clienteNTP(clienteUDP, NTP_SERVER, 3600);

void setup() {
  pinMode(botao, OUTPUT);
  pinMode(ledArduino2, OUTPUT);
  Serial.begin(115200);

  while (!Serial)
    ;

  // put your setup code here, to run once:




  WiFi.begin(SSID, PASS_WIFI);
  WiFi.status();
  while (WiFi.status() != WL_CONNECTED) {
    Serial.println(".");
    delay(500);
  }

  // imprime para ecrã
  Serial.println("Endereço IP");
  Serial.println((IPAddress)WiFi.localIP());


  Serial.println("Máscara de rede");
  Serial.println((IPAddress)WiFi.subnetMask());

  Serial.println("Default gateway");
  Serial.println((IPAddress)WiFi.gatewayIP());

  Serial.println("Potencia de sinal");
  Serial.println(WiFi.RSSI());

  //iniciar comunicacao sensor
  dht.begin();
}

void loop() {
  // receber da API

  char datahora[20];
  update_time(datahora);
  Serial.print("Data Atual: ");
  Serial.println(datahora);
  //fazer get do valor do ledArduino2

  clienteHTTP.get("/projeto/api/api.php?nome=ledArduino2");
  delay(1000);
  int response = clienteHTTP.responseStatusCode();
  //caso nao haja erro, entra no if 
  if (response == 200) {
    String HTTP = clienteHTTP.responseBody();
    Serial.print("Valor: ");
    Serial.println(HTTP);
    //se estiver o valor ligado na API, liga o LED
    if (HTTP.equals("Ligado")){
      digitalWrite(ledArduino2, HIGH);
      
    }else{
      digitalWrite(ledArduino2, LOW);
    }
    
  } else {
    Serial.println("Erro ao obter valor do ledArduino2");
  }

  //recebe o valor do botao, e liga o led caso esteja pressionado, enviando um post para a API a informar que o led ligou
  clienteHTTP.get("/projeto/api/api.php?nome=sensorNo3");  // botao
  delay(1000);
  response = clienteHTTP.responseStatusCode();
  
  if (response == 200) {
    String HTTP = clienteHTTP.responseBody();
    Serial.println(HTTP);
    int valor = HTTP.toInt(); 
    
    if (valor == 1){
      digitalWrite(botao, HIGH);
      post2API("ledArduino1","Ligado",datahora);
      
    }else{
      digitalWrite(botao, LOW);
      post2API("ledArduino1","Desligado",datahora);
    }
    
  } else {
    Serial.println("Erro ao obter luminosidade");
  }

  //enviar dados para a API



  //enviar dados
  Serial.println("A enviar humidade");
  post2API("humidade", String(dht.readHumidity()), datahora);

  Serial.println("A enviar temperatura");
  post2API("temperatura", String(dht.readTemperature()), datahora);

  delay(1000);
}


void post2API(String enviaNome, String enviaValor, String enviaHora) {
  String URLPath = "/projeto/api/api.php";
  String contentType = "application/x-www-form-urlencoded";
  String body = "nome=" + enviaNome + "&valor=" + enviaValor + "&hora=" + enviaHora;
  clienteHTTP.post(URLPath, contentType, body);

  //Enquanto a comunicação estiver ativa (connected), aguarda dados ficarem disponíveis(available)
  while (clienteHTTP.connected()) {
    if (clienteHTTP.available()) {
      int responseStatusCode = clienteHTTP.responseStatusCode();
      String responseBody = clienteHTTP.responseBody();// apenas para Gets
      Serial.println("Status Code: " + String(responseStatusCode) + " Resposta: Recebi um POST");
      Serial.println("Array\n{");
      Serial.println("\t[nome]: " + enviaNome);
      Serial.println("\t[valor]: " + enviaValor);
      Serial.println("\t[hora]: " + enviaHora + "\n}");
      //Serial.println("Status Code: " + String(responseStatusCode) + " Resposta: " + responseBody);
    }
    delay(500);
  }
}


void update_time(char *datahora){
  clienteNTP.update();
  unsigned long epochTime = clienteNTP.getEpochTime();
  sprintf(datahora, "%02d-%02d-%02d %02d:%02d:%02d", year(epochTime), month(epochTime), day(epochTime), hour(epochTime), minute(epochTime), second(epochTime));
}