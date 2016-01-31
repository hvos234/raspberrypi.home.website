#include "DHT.h"

#include <SPI.h>
#include "RF24.h"
#include <printf.h>  // Printf is used for debug

#define BAUDRATE 9600

#define MASTERPIPE 0
#define MYPIPE 3

// Uncomment whatever type you're using!
//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)
//#define DHTTYPE DHT21   // DHT 21 (AM2301)
#define DHTPIN 6     // what pin the DHT is connected to
#define UNIT 1      // 0 for Fahrenheit and 1 for Celsius

DHT dht(DHTPIN, DHTTYPE); // set dht

RF24 radio(7,8);                // nRF24L01(+) radio attached using Getting Started board 

byte pipes[][6] = {"1Node", "2Node", "3Node", "4Node", "5Node", "6Node"};

// declare actions
#define ACTIONTEMP 1 // send temperature
#define ACTIONHUM 2 // send humidity
#define ACTIONTEMPHUM 3 // send temperature and humidity

// declare variables
char payload_receive[11]; // max is 32 bytes even with enableDynamicPayloads
char payload_send[11];
char message[10];

//int task; // can not send task it gets to big
//int from;
int to;
int ac;

void setup(void)
{
  Serial.begin(BAUDRATE);
  Serial.println("vendor/TMRh20/RF24_Arduino/deviceBalcony/");
  
  Serial.println("Printf begin.");
  printf_begin(); //Printf is used for debug
  
  Serial.println("Radio begin.");
  radio.begin();
  
  radio.setPayloadSize(11);
  radio.setRetries(15,15); // optionally, increase the delay between retries & # of retries
  radio.setAutoAck(1); // Ensure autoACK is enabled  
  radio.setPALevel(RF24_PA_HIGH);
  radio.setDataRate(RF24_250KBPS);
  radio.setCRCLength(RF24_CRC_8);
  radio.setChannel(103);
  
  radio.openWritingPipe(pipes[MYPIPE]);
  radio.openReadingPipe(1,pipes[MASTERPIPE]);
  
  radio.startListening();
  
  radio.printDetails();
}

char *messageTempHum(int ac, char* message){
  char tem[5]; //2 int, 2 dec, 1 point, and \0
  char hum[5];
  
  // Reading temperature or humidity takes about 250 milliseconds!
  // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)
  float h = dht.readHumidity();
  float t = dht.readTemperature();
  float tf = t * 1.8 +32;  //Convert from C to F
  
  // check if returns are valid, if they are NaN (not a number) then something went wrong!
  if (isnan(t) || isnan(h)) {
    Serial.println("Failed read DHT");
    sprintf(message, "err:%s", "fa re");
    
  }else {
    Serial.print("Humidity: "); 
    Serial.print(h);
    Serial.print(" %\t");
    
    Serial.print("Temperature: "); 
    if (UNIT == 0 ){  //choose the right unit F or C
      Serial.print(tf);
      Serial.println(" *F");
    }
    else {
      Serial.print(t);
      Serial.println(" *C");
    }
      
    dtostrf(h, 2, 2, hum);  //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
    dtostrf(t, 2, 2, tem);
    
    // build message
    if(ACTIONTEMP == ac){ // temperature
      sprintf(message, "tem:%s", tem);
    }
    if(ACTIONHUM == ac){ // humidity
      sprintf(message, "hum:%s", hum);
    } 
    if(ACTIONTEMPHUM == ac){ // temperature and humidity
      sprintf(message, "tem:%s,hum:%s", tem, hum);
    }
  }
}

void loop(void){
  if( radio.available()){
    Serial.println("");
    
    while ( radio.available() ) {     // Is there anything ready for us?
      memset(payload_receive, 0, sizeof(payload_receive)); // clear it
      radio.read(&payload_receive, sizeof(payload_receive));
    }      
    
    Serial.print("Received: ");
    Serial.println(payload_receive);
      
    //from = 0;
    //to = 0;
    ac = 0;
    sscanf((char *)payload_receive, "ac:%d", &ac);
    
    memset(message, 0, sizeof(message)); // clear it
    if(ACTIONTEMP == ac || ACTIONHUM == ac || ACTIONTEMPHUM == ac){
      Serial.print("Action: ");
      Serial.println(ac);
      messageTempHum(ac, message);
      
    }else {
      Serial.println("Action does not exist !");
      sprintf(message, "err:%s", "ac no");
    }
    
    Serial.print("Message: ");
    Serial.println(message);
      
    memset(payload_send, 0, sizeof(payload_send)); // clear it
    sprintf(payload_send, "%s\0", message);
    
    // First, stop listening so we can talk
    radio.stopListening();
    
    Serial.print("Sending: ");
    Serial.println(payload_send);
        
    // Send the final one back.
    bool ok = radio.write( payload_send, sizeof(payload_send) );
    
    if (ok){
      Serial.println("Sending ok.");
    }else{
      Serial.println("Sending failed !");
    }
      
    // Now, resume listening so we catch the next packets.
    radio.startListening();
  }
}
