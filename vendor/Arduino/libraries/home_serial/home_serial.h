/*char *getSerial(){
    Serial.println("Serial .. ");
    char serial[21]; // ^fr:10,to:11,ac:12$ plus \0
    String serial_string = "";
    boolean start = false;
    boolean end = false;
    int i = 0;
    
    
    while (Serial.available () > 0) {
        // https://www.arduino.cc/en/Reference/ASCIIchart
        char character = Serial.read ();
        String character_string = ""; // we need this to convert the char character from ASII to readable char
        character_string.concat(character);
        //delay(10);
        
        //Serial.print("Read: ");
        //Serial.print("byte: ");
        //Serial.print(character);
        //Serial.print(" char: ");
        //Serial.println(character_string);
        
        if(94 == character){
            start = true;
            i = 0;
        }
        
        if(36 == character){
            start = false;
            end = true;
            i = 0;
        }
        
        if(start){
            serial_string += character_string;
            i++;
        }
        
        if(end){
            start = false;
            end = false;
            i = 0;
            serial_string.toCharArray(serial, 21);
            Serial.print("End: ");
            Serial.print(character_string);
            Serial.print("string: ");
            Serial.print(serial_string);
            Serial.print(" serial: ");
            Serial.println(serial);
            return serial;
        }
    }
}*/

/**
 * This is easier than the above function, because you have to change every indivuduele character from ASII to char
 * @return 
 */
char *getSerial(){
    // declare variables
    int count = 0;
    char serial[21]; // ^fr:10,to:11,ac:12$ plus \0
    
    // empty variables
    memset(&serial, 0, sizeof(serial)); // clear it
    serial[0] = '\0';
    
    Serial.println("Serial .. ");
    count = Serial.readBytesUntil('\0', serial, 21);
    
    serial[count] = '\0';
    //printf("%s\n", serial);
    // delay(1); // make sure count is correct and the whole message is received, important !!!!
    delay(1); // correction (above), i think that the delay stop the system so that the '\0' will be loaded correctly
    
    Serial.print("Serail Received ..");
    Serial.print("count: ");
    Serial.print(count);
    Serial.print(" message: ");
    Serial.println(serial);
    
    Serial.print("First char: ");
    Serial.println(serial[0]);
    if('^' != serial[0]){
        Serial.println("Error getSerial(), First character Serial wrong !");
        error.is_error = true;
        strncpy( error.message, "First character Serial wrong !", sizeof(error.message)-1 );
        return (char *) "First character Serial wrong !";
    }
    
    Serial.print("Last char: ");
    Serial.println(serial[strlen(serial)-1]);
    if('$' != serial[strlen(serial)-1]){
        Serial.println("Error getSerial(), Last character Serial wrong !");
        error.is_error = true;
        strncpy( error.message, "Last character Serial wrong !", sizeof(error.message)-1 );
        return (char *) "Last character Serial wrong !";
    }
    
    Serial.print("strlen last char: ");
    Serial.println(strlen(serial));
    Serial.print("sizeof last char: ");
    Serial.println(sizeof(serial));
    
    memmove(serial, serial+1, strlen(serial));
    serial[strlen(serial)-1] = '\0';
    delay(1); // the delay make sure that the '\0' will be loaded correctly
    return serial;
}