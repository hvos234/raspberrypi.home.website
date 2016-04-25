/*
  HomeDHT.h - Library for sending and receiving trough the rfm69.
  Created by , April 22, 2016.
  Released into the public domain.
*/

#ifndef HomeRFM69_h
#define HomeRFM69_h

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

#include "../RFM69/RFM69.h"

class HomeRFM69 
{
    public:
        HomeRFM69();
        bool initialize(uint8_t freqBand, uint8_t nodeID, uint8_t networkID, const char* key = "sampleEncryptKey", bool promiscuousMode = false, uint8_t _ack = true, uint8_t _ack_retries = 2, uint8_t _ack_wait = 30, uint8_t _timeout = 6000);
        
        boolean getError();
        char *getErrorMessage();
        void resetError();
        
        void send(int to, char *payload);
        bool sendWithAck(int to, char *payload);
        bool sendWithRetry(int to, char *payload);
        char *receive();
        char *receiveWithTimeOut();
        char *getData();
        
        char *sendAndreceiveWithTimeOut(int to, char *payload);
        char *sendWithRetryAndreceiveWithTimeOut(int to, char *payload);
        
        int getSenderId();
        int getRssi();    
    
    private:
        RFM69 *radio;

        boolean _error;
        char _error_message[50];
        
        bool ack;
        uint8_t ack_retries;
        uint8_t ack_wait;
        unsigned long timeout;
};

#endif