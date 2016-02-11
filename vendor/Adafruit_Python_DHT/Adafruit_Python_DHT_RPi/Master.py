#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys

print 'Number of arguments:', len(sys.argv), 'arguments.'
print 'Argument List:', str(sys.argv)

# first [0] argument is master
try:
    fr = sys.argv[1]
except IndexError:
   print "err;no from"
   sys.exit(0)

try:
    to = sys.argv[2]
except IndexError:
   print "err;no to"
   sys.exit(0)

try:
    ac = sys.argv[3]
except IndexError:
   print "err;no action"
   sys.exit(0)

import Adafruit_DHT

#sensor = Adafruit_DHT.DHT11
#sensor = Adafruit_DHT.DHT22
sensor = Adafruit_DHT.AM2302

pin = 4

# Try to grab a sensor reading.  Use the read_retry method which will retry up
# to 15 times to get a sensor reading (waiting 2 seconds between each retry).
humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)

# Un-comment the line below to convert the temperature to Fahrenheit.
# temperature = temperature * 9/5.0 + 32

# Note that sometimes you won't get a reading and
# the results will be null (because Linux can't
# guarantee the timing of calls to read the sensor).  
# If this happens try again!
if humidity is not None and temperature is not None:
	if '1' == ac:
		print 'tem={0:0.2f}'.format(temperature)
		sys.exit(0)
	elif '2' == ac:
		print 'hum={0:0.2f}'.format(humidity)
		sys.exit(0)
	elif '3' == ac:
		print 'tem={0:0.2f},hum={1:0.2f}'.format(temperature, humidity)
		sys.exit(0)
	else:
		print 'err:action does not exist !'
		sys.exit(1)
else:
	print 'err:failed to get read DHT !'
	sys.exit(1)
