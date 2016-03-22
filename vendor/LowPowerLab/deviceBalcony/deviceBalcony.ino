#include <RFM69.h>
#include <SPI.h>

#include <stdlib.h> // used for the dtostrf function
#include "DHT.h"

#define NODEID      3
#define NETWORKID   100
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define SERIAL_BAUD 9600

boolean requestACK = false;
bool promiscuousMode = false; //set to 'true' to sniff all packets on the same network
RFM69 radio;

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

char payload_receive[27]; // ac;13|msg;t:22.90,h:41.30 + \0 i think
char payload_send[27];
char message[17]; // size of t:22.90,h:41.30 + \0 i think

int ac;
char msg[17]; // same as message

void setup() {
  Serial.begin(SERIAL_BAUD);
  delay(10);
  radio.initialize(FREQUENCY,NODEID,NETWORKID);
  //radio.setHighPower(); //uncomment only for RFM69HW!
  
  // this change the bitrate from 4800 to 1200, and Frequenty level to max
  radio.writeReg(0x03,0x68);      //RegBitrateMsb 1200 bitrate
  radio.writeReg(0x04,0x2B);      //RegBitrateLsb 1200 bitrate
  radio.writeReg(0x05,0x00);      //RegFdevMsb     2000 
  radio.writeReg(0x06,0x52);      //RegFdevLsb     2000
  radio.writeReg(0x19,0x40|0x10|0x05);      //RegRxBw  DccFreq:010, RxBw_Mant:24, RxBw_Exp:5 
  radio.writeReg(0x18,0x00|0x00|0x01);      //RegLna  LnaZin:50ohm, LowPower:Off, CurrentGain:MAX
  
  radio.encrypt(KEY);
  radio.promiscuous(promiscuousMode);
  
  Serial.println("Setup Finished !");
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
  //check for any received packets
  if (radio.receiveDone())
  {
    
    memset(&payload_receive, 0, sizeof(payload_receive)); // clear it
    for (byte i = 0; i < radio.DATALEN; i++)
    {
      payload_receive[i] = (char)radio.DATA[i];
    }
    
    Serial.println("Received ..");
    Serial.print("Sender id: ");
    Serial.println(radio.SENDERID, DEC);
    Serial.print("Sender RX RSSI: ");
    Serial.println(radio.readRSSI());
    Serial.print("Payload size: ");
    Serial.println(sizeof(payload_receive));
    Serial.print("Payload: ");
    Serial.println(payload_receive);
    
    ac = 0;
    memset(&msg, 0, sizeof(msg)); // clear it
    sscanf((char *)payload_receive, "ac:%d,msg:%s", &ac, &msg);
    
    Serial.print("Action: ");
    Serial.println(ac);
    Serial.print("Message: ");
    Serial.println(msg);
    
    memset(&message, 0, sizeof(message)); // clear it
    if(ACTIONTEMP == ac || ACTIONHUM == ac || ACTIONTEMPHUM == ac){
      messageTempHum(ac, message);
      
    }else {
      Serial.println("Action does not exist !");
      sprintf(message, "err:%s", "ac no");
    }
    
    Serial.print("Message: ");
    Serial.println(message);
    
    Serial.print("payload_send: ");
    Serial.println(payload_send);
    
    memset(&payload_send, 0, sizeof(payload_send)); // clear it
    Serial.print("payload_send2: ");
    Serial.println(payload_send);
    
    sprintf(payload_send, "ac:%d,msg:%s", ac, message);
    
    Serial.print("payload_send3: ");
    Serial.println(payload_send);
    
    Serial.println("Sending ..");
    Serial.print("Payload size: ");
    Serial.println(sizeof(payload_send));
    Serial.print("Payload: ");
    Serial.println(payload_send);
    radio.send(radio.SENDERID, (const void*)(&payload_send), sizeof(payload_send), false);
    delay(25); // make sure payload is send
  }
}
