import os
import serial
import time
import sys

lines = []
def listPorts ():
   global lines
   ls = os.popen ( 'ls /dev/ttyUSB*').read()  
   lines = ls.split ( '\n')
            
listPorts()
      
if sys.argv.__len__() > 2: 
   phone = sys.argv[1]
   message = sys.argv[2]
   print 'Send message: ' + message + ' to phone number: ' + phone 
   '''
   AT
   AT+CMGF=1
   AT+CMGS="+15554437621"   
   >test msg!
   cntlZ
   
   # Find which port responds with: GPRS
   for portName in lines:
      if portName.strip() != '':
         comport = serial.Serial ( portName, 115200, timeout = 0.01)
         startTime = time.time()
         while (True):
            line = comport.readline().strip()
            if line != '':
               if line == 'GPRS': 
                  print 'Send message to gprs' 
                  comport.write (message )
   '''
else: 
   print 'Usage: python listPorts.py phone message' 


