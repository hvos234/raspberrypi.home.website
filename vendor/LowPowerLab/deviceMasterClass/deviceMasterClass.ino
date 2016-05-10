// HomeSerail
#include <HomeSerial.h>
HomeSerial homeserial;

// max serial is ts:99;ac:99;msg:t:99.99,h:99.99 is 31 plus ^$ plus \0
char serial[35];
int from;
int to;
int task;
int action;
// max message is t:99.99,h:99.99 is 15 plus \0
char message[17];

// HomeDHT
//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)
//#define DHTTYPE DHT21   // DHT 21 (AM2301)
#define DHTPIN 6     // what pin the DHT is connected to

#include <DHT.h> // work 1.0, 1.1
#include <HomeDHT.h>

HomeDHT homedht(DHTPIN, DHTTYPE); // work 1.0

// temperature or humdity 20.02 is 5 plus \0
char temperature[7];
char humdity[7];

// HomeRFM69
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define NODEID      1
#define NETWORKID   100
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define PROMISCUOUSMODE  false //set to 'true' to sniff all packets on the same network
#define ACK         true
#define ACK_RETRIES 2
#define ACK_WAIT    40
#define TIMEOUT     3000 // wait for respones

#include <RFM69.h>
#include <SPI.h>
#include <HomeRFM69.h>

HomeRFM69 homerfm69;

/*
to 
raspberry Pi  ->  master        fr:99;to:99;ts:99;ac:99
master        ->  device        ts:99;ac:99

back
device        ->  master        ts:99;ac:99;msg:t:99.99,h:99.99
master        ->  raspberry Pi  ts:99;ac:99;msg:t:99.99,h:99.99 
*/

// max payload or data is ts:99;ac:99;msg:t:99.99,h:99.99 is 31 plus \0
char payload[33];
char data[33];

// rest
#define SERIAL_BAUD 9600

// actions
#define ACTIONTEMP 1 // send temperature
#define ACTIONHUM 2 // send humidity
#define ACTIONTEMPHUM 3 // send temperature and humidity

void setup() {
  Serial.begin(SERIAL_BAUD);
  homerfm69.initialize(FREQUENCY, NODEID, NETWORKID, KEY, PROMISCUOUSMODE, ACK, ACK_RETRIES, ACK_WAIT, TIMEOUT);
  
  Serial.println("Setup Finished !");
}

void loop() {
  //process any serial input
  if (Serial.available() > 0) {
    memset(&serial, 0, sizeof(serial)); // clear it
    strncpy( serial, homeserial.readSerial(), sizeof(serial)-1 );
    
    if(homeserial.getError()){
      //Serial.print("Serial Error:  ");
      //Serial.println(homeserial.getErrorId());
    }else {
      Serial.print("Serial Received: ");
      Serial.println(serial);
      
      if(!homeserial.sscanfSerial(serial)){
        //Serial.print("Serial Sscanf Error:  ");
        //Serial.println(homeserial.getErrorId());
      }else {
        
        from = homeserial.getFrom();
        to = homeserial.getTo();
        task = homeserial.getTask();
        action = homeserial.getAction();
        memset(&message, 0, sizeof(message)); // clear it
        strncpy( message, homeserial.getMessage(), sizeof(message)-1 );
        
        /*Serial.print("Serial Sscanf: ");
        Serial.print("From: ");
        Serial.print(from);
        Serial.print(" To: ");
        Serial.print(to);
        Serial.print(" Task: ");
        Serial.print(task);
        Serial.print(" Action: ");
        Serial.print(action);
        Serial.print(" Message: ");
        Serial.println(homeserial.getMessage());*/
        
        if(NODEID == from && NODEID == to && 0 != task){ // if its for me
          memset(&message, 0, sizeof(message)); // clear it
          
          if(ACTIONTEMP == action){
            memset(&temperature, 0, sizeof(temperature)); // clear it
            strncpy( temperature, homedht.getTemperature(1), sizeof(temperature)-1 );
  
            if(homedht.getError()){
              //Serial.print("Temperature Error:  ");
              //Serial.println(homedht.getErrorId());
              sprintf(message, "err:%d", homedht.getErrorId());
              
            }else {
              //Serial.print("Temperature: ");
              //Serial.println(temperature);
              sprintf(message, "t:%s", temperature);
            }
          }
          
          if(ACTIONHUM == action){
            memset(&humdity, 0, sizeof(humdity)); // clear it
            strncpy( humdity, homedht.getHumdity(), sizeof(humdity)-1 );
            
            if(homedht.getError()){
              //Serial.print("Humdity Error:  ");
              //Serial.println(homedht.getErrorId());
              sprintf(message, "err:%d", homedht.getErrorId());
            }else {
              //Serial.print("Humdity: ");
              //Serial.println(humdity);
              sprintf(message, "h:%s", humdity);
            }
          }
          
          if(ACTIONTEMPHUM == action){
            memset(&temperature, 0, sizeof(temperature)); // clear it
            strncpy( temperature, homedht.getTemperature(1), sizeof(temperature)-1 );
  
            if(homedht.getError()){
              //Serial.print("Temperature Error:  ");
              //Serial.println(homedht.getErrorId());
              sprintf(message, "err:%d", homedht.getErrorId());
            }else {
              //Serial.print("Temperature: ");
              //Serial.println(temperature);
            
              memset(&humdity, 0, sizeof(humdity)); // clear it
              strncpy( humdity, homedht.getHumdity(), sizeof(humdity)-1 );
              
              if(homedht.getError()){
                //Serial.print("Humdity Error:  ");
                //Serial.println(homedht.getErrorId());
                sprintf(message, "err:%d", homedht.getErrorId());
              }else {
                //Serial.print("Humdity: ");
                //Serial.println(humdity);
                sprintf(message, "t:%s,h:%s", temperature, humdity);
              }
            }
          }
          
          memset(&serial, 0, sizeof(serial)); // clear it
          sprintf(serial, "ts:%d;ac:%d;msg:%s", task, action, message);
          
          Serial.print("Serial write: ");
          Serial.println(serial);
          homeserial.writeSerial(serial);
        }
        
        if(NODEID == from && 1 != to && 0 != to && 0 != action && 0 != task){ // if its for someone else
          memset(&message, 0, sizeof(message)); // clear it
          
          memset(&payload, 0, sizeof(payload)); // clear it
          sprintf(payload, "ts:%d;ac:%d", task, action);
          
          Serial.print("Sending:  ");
          Serial.println(payload);
          
          memset(&data, 0, sizeof(data)); // clear it
          strncpy( data, homerfm69.sendWithRetryAndreceiveWithTimeOut(to, payload, sizeof(payload)), sizeof(data)-1 );
          
          if(homerfm69.getError()){
            //Serial.print("RFM69, sendinging and receiving Error:  ");
            //Serial.println(homerfm69.getErrorId());
            sprintf(message, "err:%d", homerfm69.getErrorId());
            
          }else {
            //Serial.print("RFM69, sendinging and receiving received: ");
            //Serial.println(data);
            
            Serial.print("Received:  ");
            Serial.println(data);
            
            if(!homerfm69.sscanfData(data)){
              //Serial.print("RFM69 Sscanf Error:  ");
              //Serial.println(homerfm69.getErrorId());
              sprintf(message, "err:%d", homerfm69.getErrorId());
              
            }else {
              task = homerfm69.getTask();
              action = homerfm69.getAction();
              memset(&message, 0, sizeof(message)); // clear it
              sprintf(message, "%s", homerfm69.getMessage());
              
              if(homeserial.getTask() != homerfm69.getTask()){
                // give the data to pi and wait again
                Serial.print("Serial write and wait: ");
                Serial.println(serial);
                homeserial.writeSerial(serial);
                
                memset(&data, 0, sizeof(data)); // clear it
                strncpy( data, homerfm69.receiveWithTimeOut(), sizeof(data)-1 );
                if(!homerfm69.sscanfData(data)){
                  //Serial.print("RFM69 Sscanf Error:  ");
                  //Serial.println(homerfm69.getErrorId());
                  sprintf(message, "err:%d", homerfm69.getErrorId());
                  
                }else {
                  Serial.print("Received second:  ");
                  Serial.println(data);
                  
                  task = homerfm69.getTask();
                  action = homerfm69.getAction();
                  memset(&message, 0, sizeof(message)); // clear it
                  sprintf(message, "%s", homerfm69.getMessage());
                }
              }
            }
          }
          
          memset(&serial, 0, sizeof(serial)); // clear it
          sprintf(serial, "ts:%d;ac:%d;msg:%s", task, action, message);
          
          Serial.print("Serial write: ");
          Serial.println(serial);
          homeserial.writeSerial(serial);
        }
      } 
    }
  }
  
  //process any receiving data
  if (homerfm69.receiveDone()){
    memset(&message, 0, sizeof(message)); // clear it

    memset(&data, 0, sizeof(data)); // clear it
    strncpy( data, homerfm69.getData(), sizeof(data)-1 );
    
    Serial.print("Received done:  ");
    Serial.println(data);
    homerfm69.sendACK();
    
    Serial.print("Serial write done: ");
    Serial.println(data);
    homeserial.writeSerial(data);
    
    
  }
}
