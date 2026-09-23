// | MFRC522 Pin | NodeMCU Pin |
// | ----------- | ----------- |
// | SDA (SS)    | D2          |
// | SCK         | D5          |
// | MOSI        | D7          |
// | MISO        | D6          |
// | RST         | D1          |
// | GND         | GND         |
// | 3.3V        | 3.3V        |

// esp -> LED -> RESISTOR -> GND
// D0 -> GREEN LED
// D4 -> RED LED
// D8 -> BUZZER
//*******************************libraries********************************
//RFID-----------------------------
#include <SPI.h>
#include <MFRC522.h>
//NodeMCU--------------------------
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
//************************************************************************
#define SS_PIN D2   //D2
#define RST_PIN D1  //D1

#define GREEN_LED D0
#define RED_LED D4
#define BUZZER D8

void beepSuccess(int duration = 200) {
  tone(BUZZER, 1500);  // high pitch
  delay(duration);
  noTone(BUZZER);
}

void beepError(int duration = 500) {
  for (int i = 0; i < 2; i++) {
    tone(BUZZER, 400);  // low pitch
    delay(duration);
    noTone(BUZZER);
    delay(duration);
  }
}
//************************************************************************
MFRC522 mfrc522(SS_PIN, RST_PIN);  // Create MFRC522 instance.
//************************************************************************
const char *ssid = "Pa kiss";
const char *password = "kaibiganko";

// const char *ssid = "Deco Wifi";
// const char *password = "!Pabilikanarin01";
const char *device_token = "a1d2a7c6b7250505";
//************************************************************************
String URL = "http://192.168.29.57/attentrack/admin/getdata.php";  //computer IP or the server domain
// String URL = "http://192.168.0.116/attentrack/admin/getdata.php";  //computer IP or the server domain
// String URL = "http://10.82.138.57/attentrack/admin/getdata.php";  //computer IP or the server domain
String getData, Link;
String OldCardID = "";
unsigned long previousMillis = 0;
//************************************************************************
void setup() {
  delay(1000);
  Serial.begin(115200);
  SPI.begin();         // Init SPI bus
  SPI.setFrequency(10000000);
  mfrc522.PCD_Init();  // Init MFRC522 card
  //---------------------------------------------
  connectToWiFi();
  //---------------------------------------------
  pinMode(GREEN_LED, OUTPUT);
  pinMode(RED_LED, OUTPUT);
  pinMode(BUZZER, OUTPUT);

  digitalWrite(GREEN_LED, LOW);
  digitalWrite(RED_LED, LOW);
  digitalWrite(BUZZER, LOW);
}
//************************************************************************
void loop() {
  //check if there's a connection to Wi-Fi or not
  if (!WiFi.isConnected()) {
    connectToWiFi();  //Retry to connect to Wi-Fi
  }
  //---------------------------------------------
  if (millis() - previousMillis >= 15000) {
    previousMillis = millis();
    OldCardID = "";
  }
  // delay(50);
  //---------------------------------------------
  //look for new card
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return;  //got to start of loop if there is no card present
  }
  // Select one of the cards
  if (!mfrc522.PICC_ReadCardSerial()) {
    return;  //if read card serial(0) returns 1, the uid struct contians the ID of the read card.
  }
  //   String CardID = "";

  // for (byte i = 0; i < mfrc522.uid.size; i++) {
  //   if (mfrc522.uid.uidByte[i] < 0x10) {
  //     CardID += "0";
  //   }

  //   CardID += String(mfrc522.uid.uidByte[i], HEX);
  // }

  // CardID.toUpperCase();

  String CardID = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    CardID += mfrc522.uid.uidByte[i];
  }
  //---------------------------------------------
  if (CardID == OldCardID) {
    return;
  } else {
    OldCardID = CardID;
  }
  //---------------------------------------------
  //  Serial.println(CardID);
  SendCardID(CardID);
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
  // delay(200);
}
//************send the Card UID to the website*************
void SendCardID(String Card_uid) {
  Serial.println("Sending the Card ID");
  if (WiFi.isConnected()) {
    HTTPClient http;  //Declare object of class HTTPClient
    //GET Data
    getData = "?card_uid=" + String(Card_uid) + "&device_token=" + String(device_token);  // Add the Card ID to the GET array in order to send it
    //GET methode
    Link = URL + getData;
    WiFiClient client;
    http.begin(client, Link);
    //initiate HTTP request   //Specify content-type header

    int httpCode = http.GET();          //Send the request
    String payload = http.getString();  //Get the response payload

    //    Serial.println(Link);   //Print HTTP return code
    Serial.println(httpCode);  //Print HTTP return code
    Serial.println(Card_uid);  //Print Card ID
    Serial.println(payload);   //Print request response payload

    digitalWrite(GREEN_LED, LOW);
    digitalWrite(RED_LED, LOW);
    digitalWrite(BUZZER, LOW);

    if (httpCode == 200) {
      if (payload.substring(0, 5) == "login") {
        String user_name = payload.substring(5);
        //  Serial.println(user_name);

      } else if (payload.substring(0, 6) == "logout") {
        String user_name = payload.substring(6);
        //  Serial.println(user_name);

      } else if (payload == "succesful") {

      } else if (payload == "available") {
      }
      // delay(100);


      if (
        payload.substring(0, 5) == "login" || payload.substring(0, 6) == "logout" || payload == "succesful" || payload == "available") {

        digitalWrite(GREEN_LED, HIGH);
        beepSuccess(40);

      } else {

        digitalWrite(RED_LED, HIGH);
        beepError(80);
      }

      delay(100);

      digitalWrite(GREEN_LED, LOW);
      digitalWrite(RED_LED, LOW);
      http.end();  //Close connection
    } else {
      digitalWrite(RED_LED, HIGH);
      beepError(500);
      delay(50);
      digitalWrite(RED_LED, LOW);
    }
  }
}
//********************connect to the WiFi******************
void connectToWiFi() {
  WiFi.mode(WIFI_OFF);  //Prevents reconnection issue (taking too long to connect)
  delay(1000);
  WiFi.mode(WIFI_STA);
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    beepSuccess(300);
  }
  Serial.println("");
  Serial.println("Connected");

  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());  //IP address assigned to your ESP
  beepSuccess(1000);
  digitalWrite(GREEN_LED, LOW);
  digitalWrite(RED_LED, LOW);
}
//=======================================================================
