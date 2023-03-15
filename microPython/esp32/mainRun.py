
try: 
   from machine import Pin, UART
   import ntptime
   import ubinascii
   import network
   import machine
   from Connection import Connection 
   from ubinascii import hexlify
   import utime
   from Utilities import Utilities
   from LED import LED
   import urrequests
   
except Exception as ex:
   print ( 'Could not import because: ' + str(ex)) 
   
   
def myAddress():
   data = network.WLAN().ifconfig()
   print ( 'myAddress: ' + data[0] ) 
   return data[0]
   
def status(message):
   print (utilities.timestamp() + ' ' + message)
   
print ( 'V1.00')              
              
# Standalone objects
led = LED()
led.purple()
utilities = Utilities()
   
#Change these to match your system   
ssid     = 'BlackCheetah2' 
password = 'ZPGMXpEezQ'

connection = Connection (ssid, password )

if network.WLAN().isconnected(): 
   led.blue()
   print ( 'I am connected' )
   MAC = '1234'
   fahrenheit = '98.6'
   url = '/Paulware/updateSensor.php?MAC=' + str(MAC) + \
         '&value=' + str(fahrenheit) + ' HTTP/1.1\r\nHost: Paulware\r\nConnection: keep-alive\r\nAccept: */*\r\n\r\n'   
   print (url)
   r = urequests.get(url)
   print ( 'done with request')
else:
   print ( 'I am not WLAN connected') 
   
timeout = 0
count = 0
greenTimeout = 0
wasConnected = False 
timeout = 0
greenTimeout = 0 
while True:
  if network.WLAN().isconnected(): 
     if led.state == 1:
        led.state = 2
        greenTimeout = utime.ticks_ms() + 3000 
     else:
        if utime.ticks_ms() > greenTimeout: 
           led.state = 1 
  else:  
     Utilities.print ( 'Wifi Connection was lost, reconnect')
     connection.reset()
     
  led.update()
Utilities.print ( 'Done' )

   

