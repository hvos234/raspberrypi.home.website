#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, argparse
#task_id = sys.argv[1]
#from_device_id = sys.argv[2]
#to_device_id = sys.argv[3]
#action_id = sys.argv[4]
#data = sys.argv[5]

#import argparse

#FR:2:TO:1:TS:0:AC:1:MSG:36.00;24.00
# add arguments for the command line partameters
parser = argparse.ArgumentParser()
parser.add_argument('-ts', '--task', help="give the task id", dest='ts', default='123')
parser.add_argument('-fr', '--from', help="give the device id from witch it will send", dest='fr', default='1')
parser.add_argument('-to', '--to', help="give the device id witch it will send to", dest='to', default='2')
parser.add_argument('-ac', '--action', help="give the action id", dest='ac', default='3')
parser.add_argument('-msg', '--message', help="give data to send with it", dest='msg', default='None')

args = parser.parse_args()

task_id = args.ts
from_device_id = args.fr
to_device_id = args.to
action_id = args.ac
data = args.msg

# logging
from home_transmitter_logging import logger
logger.info("Home Transmitter Starting !")

# imports
import serial, subprocess, time  

# cache variables
#task_id = sys.argv[1]
#from_device_id = sys.argv[2]
#to_device_id = sys.argv[3]
#action_id = sys.argv[4]
#data = sys.argv[5]

# declare variables
#ser = None
status = "send"
writeMessage = ""

string = ""
readMessage = []

# the subprocess.call by start and stop will return failt when you try to
# start the daemon if it is already started this is also with stop, the status
# is diffrent it will return zero if the daemon is started and not zero if the
# daemon is stopped, so will have to check wit status if the start or stop command
# was succesfull
def home_daemon_receiver_start():
    global ser
    if not subprocess.call(['sudo', 'service', 'home-daemon-receiver', 'start']):
        logger.info("Home Transmitter subprocess.call (home-daemon-receiver, start) succesfull !")
        
        # check if the daemon is started, if not try one more time
        if not home_daemon_receiver_status():
            logger.error("Home Transmitter home-daemon-receiver is still not running after start command !")
            
            if not subprocess.call(['sudo', 'service', 'home-daemon-receiver', 'start']):
                logger.info("Home Transmitter second subprocess.call (home-daemon-receiver, start) succesfull !")
                
                if not home_daemon_receiver_status():
                    logger.error("Home Transmitter home-daemon-receiver is still not running after second start command !")
                    ser.close()
                    print "Home Daemon will not start !"
                    sys.exit(1)
                else:
                    logger.info("Home Transmitter home-daemon-receiver Started after second command !")
                    return True
            else:
                logger.error("Home Transmitter second subprocess.call (home-daemon-receiver, start) failt !")
                ser.close()
                print "Home Daemon will not start !"
                sys.exit(1);
        else:
            logger.info("Home Transmitter home-daemon-receiver Started !")
            return True
    else:
        logger.error("Home Transmitter subprocess.call (home-daemon-receiver, start) failt !")
        ser.close()
        print "Home Daemon will not start !"
        sys.exit(1);

def home_daemon_receiver_status():
    if not subprocess.call(['sudo', 'service', 'home-daemon-receiver', 'status']):
        # it is running
        logger.info("Home Transmitter home-daemon-receiver is Running !")
        return True
    else:
        # it is not running
        logger.info("Home Transmitter home-daemon-receiver is not Running !")
        return False

def home_daemon_receiver_stop():
    if not subprocess.call(['sudo', 'service', 'home-daemon-receiver', 'stop']):
        logger.info("Home Transmitter subprocess.call (home-daemon-receiver, stop) succesfull !")
        
        # check if the daemon is not stopped, and check again in 11 seconds
        # the linux daemon will give the stop command and if after 10
        # seconds it is still running it will kill it
        if home_daemon_receiver_status():
            logger.error("Home Transmitter home-daemon-receiver is still running after stop command !")
            time.sleep(11) # delays for 11 seconds
            
            if home_daemon_receiver_status():
                logger.error("Home Transmitter home-daemon-receiver is still running after stop command and wait 11 seconds !")
                print "Home Daemon will not stop !"
                sys.exit(1)
            else:
                logger.info("Home Transmitter home-daemon-receiver Stopped after waiting 11 seconds !")
                return True
        else:
            logger.info("Home Transmitter home-daemon-receiver Stopped !")
            return True  
    else:
        logger.error("Home Transmitter subprocess.call (home-daemon-receiver, stop) failt !")
        print "Home Daemon will not stop !"
        sys.exit(1);

home_daemon_receiver_stop()

def serial_connect():
    try:
        #global ser
        ser = serial.Serial('/dev/ttyAMA0', 9600, timeout=1)
        ser.open()
        return ser
    except serial.SerialException as e:
        logger.error("Failt creating / opening Serial connection, error: at %s" % str(e))
        home_daemon_receiver_start()
        print "Can not get Serial connection !"
        sys.exit(1)

ser = serial_connect()

while True:
    if status == "send":
        writeMessage = "FR:" + from_device_id + ":TO:" + to_device_id + ":TS:" + task_id + ":AC:" + action_id + ":MSG:" + data
        logger.info("Home Transmitter Transmit to serial !")
        ser.write("^" + writeMessage + "$")
        status = "receive"
    
    elif status == "receive":
        while True:
            if not ser.isOpen():
                ser = serial_connect()
                            
            #inbuff = ser.inWaiting()
            #if inbuff > 0:
            for character in ser.read():
                if character == "^":
                    string = ""
                elif character == "$":
                    # FR:2:TO:1:TS:0:AC:1:MSG:36.00;24.00
                    # split the string into a array, and check if the task
                    # id is the same as task_id 
                    readMessage = string.split(':')
                    # master id is 1
                    if readMessage[3] == "1":
                        if readMessage[5] == task_id:
                            logger.info("Home Transmitter Received: %s " % string)
                            print string
                            ser.close()
                            home_daemon_receiver_start()
                            sys.exit(0)
                else:
                    string += str(character)
