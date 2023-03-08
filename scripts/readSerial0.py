#!/usr/bin/env python
# serial_port_loopback.py
# Will also work on Python3.
# Serial port testing for a RaspberryPi.

from __future__ import print_function
import serial

port = "/dev/serial0"

def readScaleString(): 
   global port 
   global serialPort
   msg = ""
   while (True):
      loopback = serialPort.read(1)
      if (loopback != ""): 
         if (ord(loopback) != 10): 
            if (ord(loopback) == 13): 
               break
            else:
               msg = msg + loopback
   return msg
   
try:
   print ("Waiting for serial input" )
   serialPort = serial.Serial(port=port, baudrate=9600, \
                bytesize=serial.SEVENBITS,  stopbits=serial.STOPBITS_ONE, \
                timeout=0.1) # , parity=serial.PARITY_EVEN)
                
   serialPort.flushInput()
   serialPort.flushOutput()
   print("Opened port", port, "for testing:")
   while (True):
      msg = readScaleString()
      print ("[" + msg + "]")
      
except IOError:
   print ("Failed at", port, "\n")
finally: 
   serialPort.close()