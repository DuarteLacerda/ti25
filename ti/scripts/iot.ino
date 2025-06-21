  #include <WiFi.h>
  #include <HTTPClient.h>
  #include <Wire.h>
  #include <SHTSensor.h>

  // WiFi
  const char* ssid = "Beast";
  const char* password = "9908990899";

  // API
  const char* serverName = "http://192.168.1.27/Arduino-Projects/api/api.php";

  // I2C para SHT3X
  #define SDA_PIN 33
  #define SCL_PIN 32

  // Ventoinha (simulada com LED)
  #define FAN_PIN 13
  bool manualControl = false;

  SHTSensor sht(SHTSensor::SHT3X);  // Especifica modelo para segurança
  float temperature, humidity;
  int fanStatus = 0;

  float lastTemp = -1000;
  float lastHum = -1000;
  int lastFan = -1;

  void sendToServer(const String& nome, const String& valor) {
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(serverName);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");
      String postData = "nome=" + nome + "&valor=" + valor;
      int httpResponseCode = http.POST(postData);
      Serial.printf("POST %s = %s -> HTTP %d\n", nome.c_str(), valor.c_str(), httpResponseCode);
      http.end();
    } else {
      Serial.println("WiFi desconectado.");
    }
  }

  bool readTemperatureHumidity(float& temp, float& hum) {
    if (sht.readSample()) {
      temp = sht.getTemperature();
      hum = sht.getHumidity();
      return true;
    }
    return false;
  }

  void controlarVentoinha(float temp) {
    if (manualControl) return;  // não mexe se manual

    int novoEstado = (temp >= 29.0) ? HIGH : LOW;
    if (digitalRead(FAN_PIN) != novoEstado) {
      digitalWrite(FAN_PIN, novoEstado);
      fanStatus = (novoEstado == HIGH) ? 1 : 0;
    }
  }

  void lerComandosDashboard() {
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(String(serverName) + "?get=1");
      int code = http.GET();

      if (code == 200) {
        String resposta = http.getString();
        Serial.println("Resposta completa: " + resposta);

        int startIndex = 0;
        while (startIndex < resposta.length()) {
          int spaceIndex = resposta.indexOf('\n', startIndex);
          if (spaceIndex == -1) spaceIndex = resposta.length();

          String comando = resposta.substring(startIndex, spaceIndex);
          int sep = comando.indexOf(';');

          if (sep != -1) {
            String nome = comando.substring(0, sep);
            String valorStr = comando.substring(sep + 1);

            nome.trim();
            valorStr.trim();

            Serial.printf("Comando: %s = %s\n", nome.c_str(), valorStr.c_str());

            if (nome == "ventoinha") {
              int valor = valorStr.toInt();
              int novoEstado = (valor != 0) ? HIGH : LOW;
              if (digitalRead(FAN_PIN) != novoEstado) {
                digitalWrite(FAN_PIN, novoEstado);
                fanStatus = (novoEstado == HIGH) ? 1 : 0;
                Serial.printf("Ventoinha controlada: %d\n", fanStatus);
              }
            }
            // Se quiseres, podes adicionar mais ifs para outros comandos
          }

          startIndex = spaceIndex + 1;
        }

      } else {
        Serial.printf("Erro ao obter comandos: %d\n", code);
      }
      http.end();
    }
  }

  void setup() {
    Serial.begin(115200);
    delay(1000);

    pinMode(FAN_PIN, OUTPUT);
    digitalWrite(FAN_PIN, LOW);

    Wire.begin(SDA_PIN, SCL_PIN);
    sht = SHTSensor(SHTSensor::SHT3X); // força o tipo

    if (sht.init()) {
      Serial.println("SHT sensor inicializado.");
      sht.setAccuracy(SHTSensor::SHT_ACCURACY_HIGH);
    } else {
      Serial.println("Erro ao iniciar o SHT sensor!");
    }

    WiFi.begin(ssid, password);
    Serial.print("A ligar ao WiFi...");
    while (WiFi.status() != WL_CONNECTED) {
      delay(1000);
      Serial.print(".");
    }
    Serial.println("\nLigado ao WiFi com sucesso!");
  }

  void loop() {
    if (readTemperatureHumidity(temperature, humidity)) {
      Serial.printf("Temp: %.2f ºC | Hum: %.2f %%\n", temperature, humidity);

      controlarVentoinha(temperature);

      if (abs(temperature - lastTemp) > 0.2) {
        sendToServer("temperatura", String(temperature, 2));
        lastTemp = temperature;
      }

      if (abs(humidity - lastHum) > 0.5) {
        sendToServer("humidade", String(humidity, 2));
        lastHum = humidity;
      }

      if (fanStatus != lastFan) {
        sendToServer("ventoinha", String(fanStatus));
        lastFan = fanStatus;
      }
    } else {
      Serial.println("Erro ao ler o sensor SHT3x.");
    }

    lerComandosDashboard();

    delay(1000);
  }