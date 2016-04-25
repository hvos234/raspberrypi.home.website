/*
  HomeDHT.h - Library for sending and receiving trough the rfm69.
  Created by , April 22, 2016.
  Released into the public domain.
*/

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

#include "HomeRFM69.h"

HomeRFM69::HomeRFM69(){
    radio = new RFM69;
}

bool HomeRFM69::initialize(uint8_t freqBand, uint8_t nodeID, uint8_t networkID, const char* key, bool promiscuousMode, uint8_t _ack, uint8_t _ack_retries, uint8_t _ack_wait, uint8_t _timeout){
    bool successful;
    successful = radio->initialize(freqBand, nodeID, networkID);
    //radio->setHighPower(); //uncomment only for RFM69HW!

    // this change the bitrate from 4800 to 1200, and Frequenty level to max
    radio->writeReg(0x03,0x68);      //RegBitrateMsb 1200 bitrate
    radio->writeReg(0x04,0x2B);      //RegBitrateLsb 1200 bitrate
    radio->writeReg(0x05,0x00);      //RegFdevMsb     2000 
    radio->writeReg(0x06,0x52);      //RegFdevLsb     2000
    radio->writeReg(0x19,0x40|0x10|0x05);      //RegRxBw  DccFreq:010, RxBw_Mant:24, RxBw_Exp:5 
    radio->writeReg(0x18,0x00|0x00|0x01);      //RegLna  LnaZin:50ohm, LowPower:Off, CurrentGain:MAX

    radio->encrypt(key);
    radio->promiscuous(promiscuousMode); 
    
    ack = _ack;
    ack_retries = _ack_retries;
    ack_wait = _ack_wait;
    timeout = _timeout;
    
    return successful;
}

boolean HomeRFM69::getError(){
    return _error;
}

char *HomeRFM69::getErrorMessage(){
    return _error_message;
}

void HomeRFM69::resetError(){
    // reset error
    _error = false;
    memset(&_error_message, 0, sizeof(_error_message)); // clear it
}

void HomeRFM69::send(int to, char *payload){
    this->resetError();
    
    radio->send(to, (const void*)(&payload), sizeof(payload), false);
    delay(25); // make sure payload is send
    
    _error = false;
}

bool HomeRFM69::sendWithAck(int to, char *payload){
    this->resetError();
    
    uint32_t sentTime;
    
    radio->send(to, (const void*)(&payload), sizeof(payload), true);
    delay(25); // make sure payload is send
    
    sentTime = millis();
    while (millis() - sentTime < ack_wait)
    {
      if (radio->ACKReceived(to))
      {
        //Serial.print(" ~ms:"); Serial.print(millis() - sentTime);
        _error = false;
        return true;
        
      }
    }
    
    _error = true;
    strncpy( _error_message, "Error sendWithAck(), ACK not received !", sizeof(_error_message)-1 );
    
    return false;
}

bool HomeRFM69::sendWithRetry(int to, char *payload){
    this->resetError();
    
    if(!radio->sendWithRetry(to, (const void*)(&payload), sizeof(payload), ack_retries, ack_wait)){
        _error = true;
        strncpy( _error_message, "Error sendWithRetry(), ACK not received !", sizeof(_error_message)-1 );
        
        return false;
    }
    
    _error = false;
    return true; 
}

char *HomeRFM69::receive(){
    this->resetError();
    
    char data[30];
    memset(&data, 0, sizeof(data)); // clear it
    
    if (radio->receiveDone()){
        sprintf(data, "%s", this->getData());
    }
    
    if (radio->ACKRequested()){
        radio->sendACK();
    }
    
    return data;
}

char *HomeRFM69::receiveWithTimeOut(){
    this->resetError();
    
    char data[30];
    memset(&data, 0, sizeof(data)); // clear it
    
    // Wait here until we get a response, or timeout (250ms)
    unsigned long sentTime = millis();
    bool successful = true;

    // wait for respones
    while( ! radio->receiveDone() && successful ) {
      if (millis() - sentTime > timeout ) {
        successful = false;
      }
    }
    
    if (!successful) {
        sprintf(data, "%s", "Timeout !");
        _error = true;
        strncpy( _error_message, "Error receiveWithTimeOut(), Timeout !", sizeof(_error_message)-1 );
        
    }else{
        _error = false;
        sprintf(data, "%s", this->getData());
        
        if (radio->ACKRequested()){
            radio->sendACK();
        }
    }
    
    return data;
}

char *HomeRFM69::getData(){
    this->resetError();
    
    char data[30];
    memset(&data, 0, sizeof(data)); // clear it
    
    for (byte i = 0; i < radio->DATALEN; i++) {
      data[i] = (char)radio->DATA[i];
    }
    
    return data;
}

int HomeRFM69::getSenderId(){
    return radio->SENDERID;
}

int HomeRFM69::getRssi(){
    return radio->readRSSI();
}

char *HomeRFM69::sendAndreceiveWithTimeOut(int to, char *payload){
    this->resetError();
    
    char data[30];
    memset(&data, 0, sizeof(data)); // clear it
    
    this->send(to, payload);
    if(this->getError()){
        sprintf(data, "Error sendAndreceiveWithTimeOut(): %s", this->getErrorMessage());
        return data;
    }
    
    sprintf(data, "%s", this->receiveWithTimeOut());
    if(this->getError()){
        memset(&data, 0, sizeof(data)); // clear it
        sprintf(data, "Error sendAndreceiveWithTimeOut(): %s", this->getErrorMessage());
    }
    
    return data;
}
    
char *HomeRFM69::sendWithRetryAndreceiveWithTimeOut(int to, char *payload){
    this->resetError();
    
    char data[30];
    memset(&data, 0, sizeof(data)); // clear it
    
    this->sendWithAck(to, payload);
    if(this->getError()){
        sprintf(data, "Error sendWithRetryAndreceiveWithTimeOut(): %s", this->getErrorMessage());
        return data;
    }
    
    sprintf(data, "%s", this->receiveWithTimeOut());
    if(this->getError()){
        memset(&data, 0, sizeof(data)); // clear it
        sprintf(data, "Error sendWithRetryAndreceiveWithTimeOut(): %s", this->getErrorMessage());
    }
    
    return data;
}