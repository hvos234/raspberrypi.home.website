#include <RFM69.h>
#include <SPI.h>

#define NODEID      1
#define NETWORKID   100
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define SERIAL_BAUD 9600
#define TIMEOUT     6000 // wait for respones

boolean requestACK = false;
RFM69 radio;
bool promiscuousMode = false; //set to 'true' to sniff all packets on the same network

char payload[30];
char data[30];

// rest
#define SERIAL_BAUD 9600

#define NODETO      3

int TRANSMITPERIOD = 5000; //transmit a packet to gateway so often (in ms)

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

long lastPeriod = -1;
void loop(){
  int currPeriod = millis()/TRANSMITPERIOD;
  if (currPeriod != lastPeriod)
  {
    lastPeriod=currPeriod;
    
    memset(&payload, 0, sizeof(payload)); // clear it
    sprintf(payload, "%s", "Hello world !");
     
    if(radio.sendWithRetry(NODETO, (const void*)(&payload), sizeof(payload))){
      Serial.println("Sending ok !");
    }
    else {
      Serial.println("Sending failed !");
    }
    
    
  }
}
