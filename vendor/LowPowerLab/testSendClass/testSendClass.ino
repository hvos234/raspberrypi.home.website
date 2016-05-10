// HomeRFM69
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define NODEID      1
#define NETWORKID   100
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define PROMISCUOUSMODE  false //set to 'true' to sniff all packets on the same network
#define ACK         true
#define ACK_RETRIES 2
#define ACK_WAIT    40
#define TIMEOUT     6000 // wait for respones

#include <RFM69.h>
#include <SPI.h>
#include <HomeRFM69.h>

HomeRFM69 homerfm69;

char payload[30];
char data[30];

// rest
#define SERIAL_BAUD 9600

#define NODETO      3
int TRANSMITPERIOD = 5000; //transmit a packet to gateway so often (in ms)

void setup() {
  Serial.begin(SERIAL_BAUD);
  delay(10);
  
  homerfm69.initialize(FREQUENCY, NODEID, NETWORKID, KEY, PROMISCUOUSMODE, ACK, ACK_RETRIES, ACK_WAIT, TIMEOUT);
  
  Serial.println("Setup Finished !");
}

long lastPeriod = -1;
void loop() {
  int currPeriod = millis()/TRANSMITPERIOD;
  if (currPeriod != lastPeriod)
  {
    lastPeriod=currPeriod; 
    
    memset(&payload, 0, sizeof(payload)); // clear it
    sprintf(payload, "%s", "Hello world !");
    
    memset(&data, 0, sizeof(data)); // clear it
    
    homerfm69.sendWithRetry(NODETO, payload, sizeof(payload));
            
    if(homerfm69.getError()){
      Serial.print("RFM69, sendinging and receiving Error:  ");
      Serial.println(homerfm69.getErrorId());
    }else {
      Serial.print("RFM69, sendinging and receiving received: ");
      Serial.println(data);
    }
  }
}
