/*
  HomeDHT.h - Library for reading the temperature and humidity.
  Created by , April 4, 2016.
  Released into the public domain.
*/

#ifndef HomeSerial_h
#define HomeSerial_h

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

class HomeSerial 
{
    public:
        HomeSerial();
        
        boolean getError();
        char *getErrorMessage();
        
        char *readSerial();
        char *getSerial();
        boolean sscanfSerial();
        boolean writeSerial(char *);
        
        int getFrom();
        int getTo();
        int getTask();
        int getAction();
        char *getMessage();     
    
    private:
        boolean _error;
        char _error_message[50];
        
        char _serial[50];
        int _from;
        int _to;
        int _task;
        int _action;
        char _message[30];
};

#endif