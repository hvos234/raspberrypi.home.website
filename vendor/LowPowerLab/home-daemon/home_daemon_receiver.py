#!/usr/bin/env python
# -*- coding: utf-8 -*-

# logging
from home_daemon_logging import logger
from time import strftime
logger.info("Home Daemon Receiver Starting !")

# imports
import sys, serial, MySQLdb, signal

# declare variables
con = None
cur = None
ser = None

string = ""
array = ""
query = ""

# functions
def mysql_connect():
    # create a MySQL connection, and catch any errors
    try:
        global con
        con = MySQLdb.connect('localhost', 'home', 'halloha234', 'home');
        return con;
    except MySQLdb.Error, e:
        try:
            logger.error("Failt creating MySQL connection, error: at %s, %s" % (e.args[0],e.args[1]))
        except IndexError:
            logger.error("Failt creating MySQL connection, error: at %s" % str(e))
        sys.exit(1)

con = mysql_connect()

def mysql_cursor():
    global con
    if not con.open:
        con = mysql_connect()
    with con:
        cur = con.cursor()
        return cur

def mysql_cursor_close():
    global cur
    cur.close()

def serial_connect():
    try:
        global ser
        ser = serial.Serial('/dev/ttyAMA0', 9600, timeout=1)
        ser.open()
        return ser
    except serial.SerialException as e:
        logger.error("Failt creating / opening Serial connection, error: at %s" % str(e))
        global con
        con.close()
        sys.exit(1)

ser = serial_connect()

def cleanup():
    # close Serial
    global ser
    try:
        ser.close()
    except serial.SerialException as e:
        logger.error("Failt closing Serial connection, error: at %s" % str(e))
    
    # close MySQL connection
    global con
    try:
        con.close
    except MySQLdb.Error, e:
        logger.error("Failt closing MySQL connection, error: at %s, %s" % (e.args[0],e.args[1]))
        try:
            logger.error("Failt closing MySQL connection, error: at %s, %s" % (e.args[0],e.args[1]))
        except IndexError:
            logger.error("Failt closing MySQL connection, error: at %s" % str(e))
    
    # exit system
    sys.exit(0)

# SIGTERM handler
def signal_term_handler(signal, frame):
    logger.info("Home Daemon Receiver got SIGTERM !")
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGTERM, signal_term_handler)

# SIGINT handler
def signal_int_handler(signal, frame):
    logger.info("Home Daemon Receiver got SIGINT !")
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGINT, signal_int_handler)

# run
logger.info("Home Daemon Receiver Running !")

while True:
    
    # catch one character rom the serial
    for character in ser.read():
        
        # check for the first character ^
        if character == "^":
            string = ""
        
        # check for the last character $
        elif character == "$":
            # split string (FR:2:TO:1:TS:0:AC:3:MSG:29.00;34.00)
            array = string.split(':')
            
            # insert everything into the MySQL database
            query = "INSERT INTO task(from_device_id, to_device_id, action_id, data, created_at) VALUES('%s', '%s', '%s', '%s', '%s')" % (array[1], array[3], array[7], array[9], strftime("%Y-%m-%d %H:%M:%S"))
            cur = mysql_cursor()
            cur.execute(query)
            con.commit() # Make sure data is committed to the database
            mysql_cursor_close()
        
        # if it is not the first or last character,
        # add it into string
        else:
            string += str(character)
