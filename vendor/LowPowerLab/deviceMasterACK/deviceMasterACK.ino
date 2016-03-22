#include <RFM69.h>
#include <SPI.h>

#include <stdlib.h> // used for the dtostrf function
#include "DHT.h"

#define NODEID      1
#define NETWORKID   100
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define SERIAL_BAUD 9600
#define ACK_TIME    300  // # of ms to wait for an ack

byte sendSize=0;
boolean requestACK = false;
RFM69 radio;
bool promiscuousMode = false; //set to 'true' to sniff all packets on the same network

//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)
//#define DHTTYPE DHT21   // DHT 21 (AM2301)
#define DHTPIN 6     // what pin the DHT is connected to
#define UNIT 1      // 0 for Fahrenheit and 1 for Celsius

DHT dht(DHTPIN, DHTTYPE); // set dht

// declare actions
#define ACTIONTEMP 1 // send temperature
#define ACTIONHUM 2 // send humidity
#define ACTIONTEMPHUM 3 // send temperature and humidity

char payload_receive[30]; // max is 32 bytes even with enableDynamicPayloads
char payload_send[30];
char message[15];

int fr;
int to;
int ac;
char msg[15];

//String serial_receive = "";
char serial_receive[30]; // max is 32 bytes even with enableDynamicPayloads
char serial_send[30];
char serial_message[30];

int serial_fr;
int serial_to;
int serial_ac;
char serial_msg[15];

void setup() {
  Serial.begin(SERIAL_BAUD);
  delay(10);
  radio.initialize(FREQUENCY,NODEID,NETWORKID);
  //radio.setHighPower(); //uncomment only for RFM69HW!
  radio.encrypt(KEY);
  radio.promiscuous(promiscuousMode);
  char buff[50];
  sprintf(buff, "\nTransmitting at %d Mhz...", FREQUENCY==RF69_433MHZ ? 433 : FREQUENCY==RF69_868MHZ ? 868 : 915);
  Serial.println(buff);
}

char *messageTempHum(int ac, char* message){
  char tem[10]; //2 int, 2 dec, 1 point, and \0
  char hum[10];
  
  // Reading temperature or humidity takes about 250 milliseconds!
  // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)
  float h = dht.readHumidity();
  float t = dht.readTemperature();
  float tf = t * 1.8 +32;  //Convert from C to F
  
  // check if returns are valid, if they are NaN (not a number) then something went wrong!
  if (isnan(t) || isnan(h)) {
    Serial.println("Failed read DHT");
    sprintf(message, "err:%s", "fa re");
    
  }else {
    Serial.print("Humidity: "); 
    Serial.print(h);
    Serial.print(" %\t");
    
    Serial.print("Temperature: "); 
    if (UNIT == 0 ){  //choose the right unit F or C
      Serial.print(tf);
      Serial.println(" *F");
    }
    else {
      Serial.print(t);
      Serial.println(" *C");
    }
      
    dtostrf(h, 2, 2, hum);  //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
    dtostrf(t, 2, 2, tem);
    
    Serial.print("Message: ");
    Serial.print("hum: ");
    Serial.print(hum);
    Serial.print("tem: ");
    Serial.println(tem);
    
    // build message
    if(ACTIONTEMP == ac){ // temperature
      sprintf(message, "t:%s", tem);
    }
    if(ACTIONHUM == ac){ // humidity
      sprintf(message, "h:%s", hum);
    } 
    if(ACTIONTEMPHUM == ac){ // temperature and humidity
      sprintf(message, "t:%s,h:%s", tem, hum);
    }
  }
}

void loop() {
  //process any serial input
  if (Serial.available() > 0)
  {
    memset(serial_receive, 0, sizeof(serial_receive)); // clear char array
    //serial_receive = "";
    //serial_receive = Serial.readBytesUntil('\0');
    Serial.readBytesUntil(10, serial_receive, 32);
    Serial.print("Serial Received: ");
    Serial.println(serial_receive);
    
    serial_fr = 0;
    serial_to = 0;
    serial_ac = 0;
    sscanf((char *)serial_receive, "fr:%d,to:%d,ac:%d", &serial_fr, &serial_to, &serial_ac);
    
    Serial.print("Serial From: ");
    Serial.println(serial_fr);
    Serial.print("Serial To: ");
    Serial.println(serial_to);
    Serial.print("Serial Action: ");
    Serial.println(serial_ac);
    
    
    if(NODEID == serial_fr && NODEID == serial_to){ // if its for me
      
      memset(serial_message, 0, sizeof(serial_message)); // clear it
      if(ACTIONTEMP == serial_ac || ACTIONHUM == serial_ac || ACTIONTEMPHUM == serial_ac){
        messageTempHum(ac, serial_message);
        
        Serial.print("Serial Message: ");
        Serial.println(serial_message);
        
      }else {
        Serial.println("Action does not exist !");
        sprintf(serial_message, "err:%s", "ac no");
      }
    }
    
    if(NODEID == serial_fr && 0 != serial_to && 0 != serial_ac){
      memset(serial_message, 0, sizeof(serial_message)); // clear it
      
      memset(payload_send, 0, sizeof(payload_send)); // clear it
      sprintf(payload_send, "ac:%d,msg:%s", serial_ac, message);
      
      Serial.println("Sending ..");
      Serial.print("Payload size: ");
      Serial.println(sizeof(payload_send));
      Serial.print("Payload: ");
      Serial.println(payload_send);
      if (radio.sendWithRetry(serial_to, (const void*)(&payload_send), sizeof(payload_send))){
        Serial.println("Sending ok !");
      }
      else {
        Serial.print("Sending failed !");
      }
    }
  }
  
  //check for any received packets
  if (radio.receiveDone())
  {
    Serial.print('[');
    Serial.print(radio.SENDERID, DEC);
    Serial.print("] ");
    memset(payload_receive, 0, sizeof(payload_receive)); // clear it
    for (byte i = 0; i < radio.DATALEN; i++)
    {
      payload_receive[i] = (char)radio.DATA[i];
    }
    Serial.println("Received ..");
    Serial.print("Payload size: ");
    Serial.println(sizeof(payload_receive));
    Serial.print("Payload: ");
    Serial.println(payload_receive);
    
    Serial.print("   [RX_RSSI:");
    Serial.print(radio.readRSSI());
    Serial.println("]");
    
    //fr = 0;
    //to = 0;
    ac = 0;
    memset(msg, 0, sizeof(msg)); // clear it
    //sscanf((char *)payload_receive, "fr:%d,to:%d,ac:%d,msg:%s", &fr, &to, &ac, &msg);
    sscanf((char *)payload_receive, "ac:%d,msg:%s", &ac, &msg);
    
    //Serial.print("From: ");
    //Serial.println(fr);
    //Serial.print("To: ");
    //Serial.println(to);
    Serial.print("Action: ");
    Serial.println(ac);
    Serial.print("Message: ");
    Serial.println(msg);
    
    //if(NODEID == to){ // if tis for me
      // return ACK
      if (radio.ACKRequested())
      {
        radio.sendACK();
        Serial.print(" - ACK sent");
      }
      Serial.println();
    //}
  }
}
