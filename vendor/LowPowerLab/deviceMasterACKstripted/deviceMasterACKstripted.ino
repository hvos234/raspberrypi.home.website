#include <RFM69.h>
#include <SPI.h>

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

char payload_receive[30]; // max is 32 bytes even with enableDynamicPayloads
char payload_send[30];
char message[15];

int TRANSMITPERIOD = 5000; //transmit a packet to gateway so often (in ms)

void setup() {
  Serial.begin(SERIAL_BAUD);
  delay(10);
  radio.initialize(FREQUENCY,NODEID,NETWORKID);
  //radio.setHighPower(); //uncomment only for RFM69HW!
  
  // this change the bitrate from 4800 to 1200, and Frequenty level to max
  /*radio.writeReg(0x03,0x68);      //RegBitrateMsb 1200 bitrate
  radio.writeReg(0x04,0x2B);      //RegBitrateLsb 1200 bitrate
  radio.writeReg(0x05,0x00);      //RegFdevMsb     2000 
  radio.writeReg(0x06,0x52);      //RegFdevLsb     2000*/
  //radio.writeReg(0x19,0x40|0x10|0x05);      //RegRxBw  DccFreq:010, RxBw_Mant:24, RxBw_Exp:5 
  //radio.writeReg(0x18,0x00|0x00|0x01);      //RegLna  LnaZin:50ohm, LowPower:Off, CurrentGain:MAX
  
  //radio.encrypt(KEY);
  //radio.promiscuous(promiscuousMode);
}

long lastPeriod = -1;
void loop() {
  int currPeriod = millis()/TRANSMITPERIOD;
  if (currPeriod != lastPeriod)
  {
    lastPeriod=currPeriod;  
    memset(payload_send, 0, sizeof(payload_send)); // clear it
    sprintf(payload_send, "ac:%d,msg:%s", "1", "hello");
    
    Serial.println("Sending ..");
    Serial.print("Payload size: ");
    Serial.println(sizeof(payload_send));
    Serial.print("Payload: ");
    Serial.println(payload_send);
    if (radio.sendWithRetry(3, (const void*)(&payload_send), sizeof(payload_send))){
      Serial.println("Sending ok !");
    }
    else {
      Serial.print("Sending failed !");
    }
  }
  
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
    
    if (radio.ACKRequested())
    {
      radio.sendACK();
      Serial.print(" - ACK sent");
    }
    Serial.println();
  }
}
