
# Items user can change 
interrupt_pin = 14
testing  = False
#Change these to match your system   
ssid     = 'pi4'   
password = 'ABCD1234' 

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

print ( 'EdgeTrigger' ) 
#   Class for edgeTrigger
#   Note: This can be moved to a separate file    
import utime   
class EdgeTrigger: 
   def __init__ (self, msTimeout): 
      self.msTimeout = msTimeout   
      self.timer     = 0 
      self.lastValue = False 
      self.value     = False 
      self.rising    = False 
      self.falling   = False 

   def update(self,value):
      self.rising  = False 
      self.falling = False 
      
      if value: 
         if not self.lastValue:      
            self.rising = True 
         self.timer = utime.ticks_ms() + self.msTimeout       
      else: 
         if self.timer != 0: 
            if utime.ticks_ms() > self.timer: 
               self.falling = True 
               self.timer = 0               
      self.lastValue = value 
   
   
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
      
def handle_interrupt(pin):
   global motion
   motion = True
   global interrupt_pin
   interrupt_pin = pin 
     
Utilities.print ( 'Motion sensor code V1.01')              
motion = False

# Standalone objects
led = LED()
led.purple()
utilities = Utilities()

# Note: If device is not logging into the pi's wifi, the server address will be different
serverAddress = '192.168.4.1' # pi's built in wifi 

pir = Pin(interrupt_pin, Pin.IN)
pir.irq(trigger=Pin.IRQ_RISING, handler=handle_interrupt)

connection = Connection (ssid, password )
if network.WLAN().isconnected(): 
   led.blue()
   sendMessage (0, testing)
else:
   Utilities.print ( 'Warning....I am not WLAN connected') 
             
edgeTrigger = EdgeTrigger (10000)
Utilities.print ( 'Running infinite sensor loop' )

if testing:
   motion = True   
while True:
   if network.WLAN().isconnected():
      edgeTrigger.update (motion)
      if edgeTrigger.rising:
         Utilities.print('Motion detected, edgeTrigger rising')
         led.red()
         if testing: 
            motion = False 

         sendMessage (1, testing)
      elif edgeTrigger.falling: 
         Utilities.print('Motion not detected for 10 seconds')
         led.green()
         sendMessage(0, testing)
         motion = False 
   else: 
      led.red() 
      Utilities.print ( 'Wifi Connection was lost, reconnect')
      connection.reset()     
   led.update()
   
Utilities.print ( 'Done' )



