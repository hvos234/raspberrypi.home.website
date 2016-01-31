#include <cstdlib>
#include <iostream>
#include <sstream>
#include <string>
#include <unistd.h>
#include <RF24/RF24.h>

/**
 * g++ -L/usr/lib main.cc -I/usr/include -o main -lrrd
 **/
using namespace std;

// Radio CE Pin, CSN Pin, SPI Speed
// See http://www.airspayce.com/mikem/bcm2835/group__constants.html#ga63c029bd6500167152db4e57736d0939 and the related enumerations for pin information.

// Setup for GPIO 22 CE and CE0 CSN with SPI Speed @ 4Mhz
//RF24 radio(RPI_V2_GPIO_P1_22, BCM2835_SPI_CS0, BCM2835_SPI_SPEED_4MHZ);

// NEW: Setup for RPi B+
//RF24 radio(RPI_BPLUS_GPIO_J8_15,RPI_BPLUS_GPIO_J8_24, BCM2835_SPI_SPEED_8MHZ);

// Setup for GPIO 15 CE and CE0 CSN with SPI Speed @ 8Mhz
//RF24 radio(RPI_V2_GPIO_P1_15, RPI_V2_GPIO_P1_24, BCM2835_SPI_SPEED_8MHZ);

// RPi generic:
RF24 radio(22,0);

/*** RPi Alternate ***/
//Note: Specify SPI BUS 0 or 1 instead of CS pin number.
// See http://tmrh20.github.io/RF24/RPi.html for more information on usage

//RPi Alternate, with MRAA
//RF24 radio(15,0);

//RPi Alternate, with SPIDEV - Note: Edit RF24/arch/BBB/spi.cpp and  set 'this->device = "/dev/spidev0.0";;' or as listed in /dev
//RF24 radio(22,0); 

// Radio pipe addresses for the 2 nodes to communicate.
const uint8_t pipes[][6] = {"1Node", "2Node", "3Node", "4Node", "5Node", "6Node"};

int main(int argc, char** argv) 
{
    
    printf("vendor/TMRh20/RF24_RPi/Task\n");
    
    // Refer to RF24.h or nRF24L01 DS for settings

    radio.begin();

    delay(5);
    
    radio.setPayloadSize(11);
    radio.setRetries(15,15); // optionally, increase the delay between retries & # of retries
    radio.setAutoAck(1); // Ensure autoACK is enabled
    radio.setPALevel(RF24_PA_HIGH);
    radio.setDataRate(RF24_250KBPS);
    radio.setCRCLength(RF24_CRC_8);
    radio.setChannel(103);
    
    radio.openWritingPipe(pipes[0]);
    radio.openReadingPipe(1,pipes[3]);
    
    radio.startListening();
    
    radio.printDetails();
    
    // First, stop listening so we can talk.
    radio.stopListening();
    
    // Take the time, and send it.  This will block until complete

    char payload_send[11] = "";
    //sprintf(payload_send, "%s", "ac:3");
    snprintf(payload_send, 11, "to:%s,ac:%s", argv[2], argv[3]);
    

    printf("Sending ..\n");
    printf(payload_send);
    printf("\n");

    bool ok = radio.write( &payload_send, sizeof(payload_send) );

    if (!ok){
        printf("failed.\n");
        return 1;
    }
    
    // Now, continue listening
    radio.startListening();

    // Wait here until we get a response, or timeout (250ms)
    unsigned long started_waiting_at = millis();
    bool timeout = false;
    while ( ! radio.available() && ! timeout ) {
        if (millis() - started_waiting_at > 3000 ) {
            timeout = true;
        }
    }


    // Describe the results
    if ( timeout ) {
        printf("Failed, response timed out.\n");
        return 1;
        
    } else {
        // Grab the response, compare, and send to debugging spew
        char payload_receive[11] = "";
        radio.read( &payload_receive, sizeof(payload_receive) );

        // Spew it
        printf("Received: ");
        printf(payload_receive);
        printf("\n");
    }
    return 0;
}

