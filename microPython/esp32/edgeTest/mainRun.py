# Items user can change 
interruptPin = 14

#  imports 
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
#      TBD: move to a separate file    
import utime   

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
              
Utilities.print ( 'Edge Trigger code V1.00')              

# Standalone objects
led = LED()
led.purple()
utilities = Utilities()
             
edgeTrigger = EdgeTrigger (interruptPin)
Utilities.print ( 'Running infinite sensor loop' )

while True:
   if edgeTrigger.rising:
      Utilities.print('Rising edge detected')
      led.red()
   elif edgeTrigger.falling: 
      Utilities.print('Falling edge detected')
      led.green()
      
   edgeTrigger.update ()
   led.update()
   
Utilities.print ( 'Done' )
