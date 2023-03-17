

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
   import urequests
   
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
temperature = 98.6
if network.WLAN().isconnected(): 
   led.blue()
   print ( 'mac: ' + connection.mac )
   print ( 'serverAddress: ' + serverAddress )
else:
   print ( 'Warning....I am not WLAN connected') 
   

sensorTimeout = utime.ticks_ms() + 3000 
print ( 'Running infinite sensor loop' )
while True:
  if network.WLAN().isconnected(): 
     if utime.ticks_ms () > sensorTimeout: 
        led.green()
        sensorTimeout = utime.ticks_ms() + 3000 
        if temperature < 100 : 
           temperature = temperature + 0.1
        else:
           temperature = 98.6
           
        url = 'http://' + serverAddress + \
              '/Paulware/updateSensor.php?MAC=' + str(connection.mac) + \
              '&value=' + str(temperature) + \
              ' HTTP/1.1\r\nHost: Paulware\r\nConnection: keep-alive\r\nAccept: */*\r\n\r\n'   
        r = urequests.get(url)
        print ( 'sensor updated ' + str(temperature)) 
  else: 
     led.red() 
     Utilities.print ( 'Wifi Connection was lost, reconnect')
     connection.reset()     
  led.update()
  
Utilities.print ( 'Done' )
