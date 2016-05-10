#define SERIAL_BAUD 9600

#define NODEID 1

typedef struct {
  boolean is_error;
  char message[30];
} Error;

Error error;

#include <RFM69.h>
#include <SPI.h>
#include "DHT.h"
#include "home_serial.h"
#include "home_temperature_humidity.h"
#include "home_transceiver.h"

// declare actions
#define ACTEMP 1 // send temperature
#define ACHUM 2 // send humidity
#define ACTEMPHUM 3 // send temperature and humidity

void setup() {
  Serial.begin(SERIAL_BAUD);
  
  home_transceiver_setup();
}

void loop() {
  //process any serial input
  if (Serial.available() > 0) {
    char serial[50];
    char message[30];
    memset(&serial, 0, sizeof(serial)); // clear it
    memset(&message, 0, sizeof(message)); // clear it
    
    strncpy( serial, home_serial_read(), sizeof(serial)-1 );
    
    Serial.print("Serial Received: ");
    Serial.println(serial);
    
    Serial.print("Error: ");
    Serial.print(error.is_error);
    Serial.print(" ");
    Serial.println(error.message);
    
    if(!error.is_error){
      home_serial_sscanf(serial);
      
      memset(&serial, 0, sizeof(serial)); // clear it
      if(NODEID == home_serial.from && NODEID == home_serial.to && 0 != home_serial.action){ // if its me
        if(ACTEMP == home_serial.action){
          char temperature[7];
          temperature = home_temperature();
          //sprintf(temperature, "%s", home_temperature());
          Serial.print("Temperature: ");
          Serial.println(temperature);
          
          if(!error.is_error){
            sprintf(message, "t:%s", temperature);
          }else {
            sprintf(message, "err:%s", error.message);
          }
          
        }else if(ACHUM == home_serial.action){
          char *humidity;
          humidity = home_humidity();
          
          if(!error.is_error){
            sprintf(message, "t:%s", humidity);
          }else {
            sprintf(message, "err:%s", error.message);
          }
          
        }else if(ACTEMPHUM == home_serial.action){
          char *temperature;
          char *humidity;
          temperature = home_temperature();
          
          if(!error.is_error){
            humidity = home_humidity();
            if(!error.is_error){
              sprintf(message, "t:%s,h:%s", temperature, humidity);
            }else {
              sprintf(message, "err:%s", error.message);
            }
          }else {
            sprintf(message, "err:%s", error.message);
          }
          
          
        }else {
           sprintf(message, "err:%s", "no action !");
        }
        
        Serial.println("Serial Return .. ");
        Serial.print("message: ");
        Serial.print(message);
        
        sprintf(serial, "fr:%d;to:%d;ts:%d;ac:%d;msg:%s", home_serial.from, home_serial.to, home_serial.task, home_serial.action, message);
        
        Serial.print(" serial: ");
        Serial.println(serial);
        
      }else if(NODEID == home_serial.from && 0 != home_serial.to && 0 != home_serial.action){ // not for me
        
      }
      
      
    }
  }
  
  //check for any received packets
  if (radio.receiveDone()) {
    char payload[30];
    strncpy( payload, home_transceiver_receive(), sizeof(payload)-1 );
    
    Serial.print("Payload Received: ");
    Serial.println(payload);
    
    Serial.print("Error: ");
    Serial.print(error.is_error);
    Serial.print(" ");
    Serial.println(error.message);
  }
}

