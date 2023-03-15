
try: 
   from machine import Pin, UART
   import ntptime
   import ubinascii
   import network
   import machine
   from ubinascii import hexlify
   import utime
   import urequests
   
   from Connection import Connection 
   from Utilities import Utilities
   from LED import LED
   
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
ssid     = 'pi4'   
password = 'ABCD1234' 
# Note: If device is not logging into the pi's wifi, the server address will be different
serverAddress = '192.168.4.1' # pi's built in wifi 

connection = Connection (ssid, password )

if network.WLAN().isconnected(): 
   led.blue()
   fahrenheit = '98.6'
   print ( 'mac: ' + connection.mac )
   print ( 'serverAddress: ' + serverAddress )
   url = 'http://' + serverAddress + \
         '/Paulware/updateSensor.php?MAC=' + str(connection.mac) + \
         '&value=' + str(fahrenheit) + \
         ' HTTP/1.1\r\nHost: Paulware\r\nConnection: keep-alive\r\nAccept: */*\r\n\r\n'   
   r = urequests.get(url)
else:
   print ( 'I am not WLAN connected') 
   

timeout = 0
count = 0
greenTimeout = 0
wasConnected = False 
timeout = 0
greenTimeout = 0 
print ( 'Running infinite loop' )
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

   

