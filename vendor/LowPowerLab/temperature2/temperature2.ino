#define SERIAL_BAUD 9600

typedef struct {
  boolean is_error;
  char message[30];
} Error;

Error error;

#include "DHT.h"
#include <stdlib.h> // used for the dtostrf function
#include "temperature_humidity.h"


void setup() {
  Serial.begin(SERIAL_BAUD);
}

void loop() {
  char temperature[7];
  strncpy( temperature, getTemperature(), sizeof(temperature)-1 );
  Serial.print("Temperature: "); 
  Serial.println(temperature);
  
  char humidity[7];
  strncpy( humidity, getHumidity(), sizeof(humidity)-1 );
  Serial.print("Humidity: "); 
  Serial.println(humidity);
  
  delay(10000);
}
