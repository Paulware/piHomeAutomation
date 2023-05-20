# Items user can change 
pinInput = 14
#Change these to match your system (if you don't want to use the pi's wifi)  
ssid     = 'pi4'   
password = 'ABCD1234' 
# Note: If ssid is changed, you need to know the server address and change the next line 
serverAddress = '192.168.4.1' # pi's built in wifi 

testing  = False
#  imports 
print ( 'imports' )
try: 
   from machine import Pin, UART
   from time import sleep
   import network
   from Connection import Connection 
   import utime
   from Utilities import Utilities
   from LED import LED
   import urequests
   
except Exception as ex:
   print ( 'Could not import because: ' + str(ex)) 

#   Class for edgeTrigger
#   TBD:  Move to a separate file    
import utime   
from machine import Pin
class EdgeTrigger: 
   def __init__ (self, pin): 
      self.pin = Pin (pin, Pin.IN)      
      self.timer     = 0 
      self.value     = self.pin.value()
      self.rising    = False 
      self.falling   = False 

   def update(self):
      self.rising  = False 
      self.falling = False       
      
      if utime.ticks_ms() > self.timer: 
         val = self.pin.value()      
         if val != self.value: 
            self.timer = utime.ticks_ms() + 400 #Debounce
            if self.value == 0:      
               self.rising = True 
            else:
               self.falling = True 
            self.value = val 
      
# pre-defined functions
def sendMessage (value, testing):
   Utilities.print ( 'mac: ' + connection.mac )
   Utilities.print ( 'serverAddress: ' + serverAddress )
   Utilities.print ( 'value: ' + str(value) )
      
   url = 'http://' + serverAddress + \
         '/Paulware/updateSensor.php?MAC=' + str(connection.mac) + \
         '&value=' + str(value) + ' HTTP/1.1\r\nHost: Paulware\r\nConnection: keep-alive\r\nAccept: */*\r\n\r\n'   
   try: 
      if not testing: 
         r = urequests.get(url)
   except Exception as ex: 
      Utilities.print ( 'Could not request url because:' + str(ex))       
           
Utilities.print ( 'Motion sensor code V1.02')              
motion = False

# Standalone objects
led = LED()
led.purple()
utilities = Utilities()

if not testing:
   connection = Connection (ssid, password )
   if network.WLAN().isconnected(): 
      led.blue()
      sendMessage (0, testing)
   else:
       Utilities.print ( 'Warning....I am not WLAN connected') 
             
edgeTrigger = EdgeTrigger (pinInput)
Utilities.print ( 'Running infinite sensor loop' )
while True:
   if not testing: 
      if network.WLAN().isconnected():
         if edgeTrigger.rising:
            Utilities.print('Motion detected, edgeTrigger rising')
            led.red()         
            sendMessage (1, testing)
         elif edgeTrigger.falling: 
            Utilities.print('Motion no longer detected')
            led.green()
            sendMessage(0, testing)
      else: 
         led.red() 
         Utilities.print ( 'Wifi Connection was lost, reconnect')
         connection.reset() 
            
   led.update()
   edgeTrigger.update()
   
Utilities.print ( 'Done' )




