/*
  HomeDHT.h - Library for reading the temperature and humidity.
  Created by , April 4, 2016.
  Released into the public domain.
*/

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

#include "HomeDHT.h"

//#include "stdlib.h" // work 1.1 (not even needed)

/*#ifndef DHT_h
    #include "../DHT/DHT.h" // work 1.0
#endif*/
//#include "../DHT/DHT.h"  // work 1.0, work 1.1 (not even needed)

HomeDHT::HomeDHT(uint8_t pin, uint8_t type, uint8_t count){ // work 1.0
    _dht = new DHT(pin, type, count); // work 1.0
}

boolean HomeDHT::getError(){
    return _error;
}

/*char *HomeDHT::getErrorMessage(){
    return _error_message;
}*/

int HomeDHT::getErrorId(){
    return _error_id;
}

void HomeDHT::resetError(){
    // reset error
    _error = false;
    //memset(&_error_message, 0, sizeof(_error_message)); // clear it
    _error_id = 0;
}

char *HomeDHT::getTemperature(int unit){
    this->resetError();
    
    // declare variables
    char temperature[7]; //2 int, 2 dec, 1 point, and \0
    float t = 0;
    float tf = 0;
    
    // empty variables
    memset(&temperature, 0, sizeof(temperature)); // clear it
    
    t = _dht->readTemperature(); // work 1.0
    tf = t * 1.8 +32;  //Convert from C to F
    
    if (isnan(t)) {
      //Serial.println("Error getTemperature(), Failed read DHT !");
      _error = true;
      //strncpy( _error_message, "Failed read DHT !", sizeof(_error_message)-1 );
      _error_id = 11;

      return (char *) "Failed read DHT !";
    }

    _error = false;

    //Serial.print("Temperature: ");
    if (0 == unit){  //choose the right unit F or C
      //Serial.print(tf);
      //Serial.println(" *F");
      //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
      dtostrf(tf, 2, 2, temperature); // dtostrf convert it to 2 before decimal and 2 after decimal
    }else {
      //Serial.print(t);
      //Serial.println(" *C");
      dtostrf(t, 2, 2, temperature); // dtostrf convert it to 2 before decimal and 2 after decimal
    }

    return temperature;
}

char *HomeDHT::getHumdity() {
    this->resetError();
    
    // declare variables
    char humidity[7]; //2 int, 2 dec, 1 point, and \0
    float h = 0;

    // empty variables
    memset(&humidity, 0, sizeof(humidity)); // clear it

    h = _dht->readHumidity(); // work 1.0
    
    if (isnan(h)) {
      //Serial.println("Error getHumidity(), Failed read DHT !");
      _error = true;
      //strncpy( _error_message, "Failed read DHT !", sizeof(_error_message)-1 );
      _error_id = 21;

      return (char *) "Failed read DHT !";
    }

    _error = false;

    //Serial.print("Humidity: "); 
    //Serial.print(h);
    //Serial.println(" %\t");

    //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
    dtostrf(h, 2, 2, humidity); // dtostrf convert it to 2 before decimal and 2 after decimal

    return humidity;
}