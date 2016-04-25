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

#include "HomeSerial.h"

HomeSerial::HomeSerial(){}

boolean HomeSerial::getError(){
    return _error;
}

char *HomeSerial::getErrorMessage(){
    return _error_message;
}

char *HomeSerial::readSerial(){
    // reset error
    _error = false;
    memset(&_error_message, 0, sizeof(_error_message)); // clear it
    
    // declare variables
    int num_bytes = 0;
    char bytes[30]; // ^fr:10;to:10;ts:12;ac:10;$ plus \0
    
    // empty variables
    memset(&bytes, 0, sizeof(bytes)); // clear it
    //bytes[0] = '\0';
    
    num_bytes = Serial.readBytesUntil('\0', bytes, sizeof(bytes));
    
    Serial.println("Serail ..");
    
    _error = true;
    strncpy( _error_message, "Serial must start with ^ and end with $ !", sizeof(_error_message)-1 );    
    
    int count = 0;
    memset(&_serial, 0, sizeof(_serial)); // clear it
    
    boolean start = false;
    for(int i = 0; i <= num_bytes; i++){        
        if('^' == bytes[i]){
            start = true;
            count = 0;
            
        }else if(start && '$' == bytes[i]){
           start = false;
           _serial[count] = '\0';
           
           _error = false;
           memset(&_error_message, 0, sizeof(_error_message)); // clear it
           
       }else if(start){
            _serial[count] = bytes[i];
            count++;
        }
    }
    
    Serial.print("Serial: ");
    Serial.println(_serial);
    
    return _serial;
}

char *HomeSerial::getSerial(){
    return _serial;
}

boolean HomeSerial::sscanfSerial(){
    _from = 0;
    _to = 0;
    _task = 0;
    _action = 0;
    memset(&_message, 0, sizeof(_message)); // clear it
    
    sscanf((char *)_serial, "fr:%d;to:%d;ts:%d;ac:%d;msg:%s", &_from, &_to, &_task, &_action, &_message); 
    
    if (0 == _from){
        _error = true;
        strncpy( _error_message, "Serial has no fr:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        return false;
    }
    
    if (0 == _to){
        _error = true;
        strncpy( _error_message, "Serial has no to:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        return false;
    }
    
    if (0 == _task){
        _error = true;
        strncpy( _error_message, "Serial has no ts:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        return false;
    }
    
    if (0 == _action){
        _error = true;
        strncpy( _error_message, "Serial has no ac:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        return false;
    }
    
    Serial.print("Serial sscanf: ");
    Serial.print("From: ");
    Serial.print(_from);
    Serial.print(" To: ");
    Serial.print(_to);
    Serial.print(" Task: ");
    Serial.print(_task);
    Serial.print(" Action: ");
    Serial.print(_action);
    Serial.print(" Message: ");
    Serial.println(_message);
    
    return true;
}

int HomeSerial::getFrom(){
    return _from;
}

int HomeSerial::getTo(){
    return _to;
}

int HomeSerial::getTask(){
    return _task;
}

int HomeSerial::getAction(){
    return _action;
}

char *HomeSerial::getMessage(){
    return _message;
}

boolean HomeSerial::writeSerial(char *serial){
    Serial.print('^');
    Serial.print(serial);
    Serial.println('$');
    
    _error = false;
    memset(&_error_message, 0, sizeof(_error_message)); // clear it
    
    return true;
}