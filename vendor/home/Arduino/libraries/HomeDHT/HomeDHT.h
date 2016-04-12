/*
  HomeDHT.h - Library for reading the temperature and humidity.
  Created by , April 4, 2016.
  Released into the public domain.
*/

#ifndef HomeDHT_h
#define HomeDHT_h

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

#include "stdlib.h"
#include "../DHT/DHT.h" // work 1.0
//class DHT;  // work also 1.0

class HomeDHT 
{
    public:
        HomeDHT(uint8_t pin, uint8_t type, uint8_t count=6); // work 1.0
        
        boolean getError();
        char *getErrorMessage();
        
        char *getTemperature(int unit);
        char *getHumdity();
    
    private:
        DHT *dht; // work 1.0

        boolean _error;
        char _error_message[50];
};

#endif