// HomeRFM69
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define NODEID      3
#define NETWORKID   100
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define PROMISCUOUSMODE  false //set to 'true' to sniff all packets on the same network
#define ACK         true
#define ACK_RETRIES 2
#define ACK_WAIT    30
#define TIMEOUT     6000 // wait for respones

#include <RFM69.h>
#include <SPI.h>

byte sendSize=0;
boolean requestACK = false;
RFM69 radio;
bool promiscuousMode = false; //set to 'true' to sniff all packets on the same network

char payload[30];
char data[30];

// rest
#define SERIAL_BAUD 9600

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

void loop() {
  //process any receiving data
  if (radio.receiveDone())
  {
    Serial.print('[');
    Serial.print(radio.SENDERID, DEC);
    Serial.print("] ");
    memset(data, 0, sizeof(data)); // clear it
    for (byte i = 0; i < radio.DATALEN; i++)
    {
      data[i] = (char)radio.DATA[i];
    }
    Serial.println("Received ..");
    Serial.print("Payload size: ");
    Serial.println(sizeof(data));
    Serial.print("Payload: ");
    Serial.println(data);
    
    Serial.print("   [RX_RSSI:");
    Serial.print(radio.readRSSI());
    Serial.println("]");
    
    delay(5);
    
    if (radio.ACKRequested())
    {
      byte theNodeID = radio.SENDERID;
      radio.sendACK();
      delay(10);
      Serial.print(" - ACK sent");
    }
  }
}
