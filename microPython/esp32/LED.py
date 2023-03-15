

import time 
from machine import Pin
import utime 
class LED: 
   def __init__(self): 
      self.r = Pin(18, Pin.OUT)
      self.b = Pin(23, Pin.OUT)
      self.g = Pin(19, Pin.OUT) 
      self.lastState = -1 
      self.blinkTimeout = 0
      self.blink = 0
      self.purple()
    
   def purple (self):
      self.set ( 0,0,1)
      self.state = 0
      print ( 'purple')
      
   def blue (self):
      self.set (1,0,1)
      self.state = 1
      print ( 'blue') 
      
   def green (self):
      self.set (1,1,0)
      self.state = 2
      print ( 'green')
      
   def red (self):
      self.set (0,1,1)
      self.state = 3 
      print ( 'red')
      
   def set(self,red,blue,green):
      self.r.value(red)
      self.b.value(blue)
      self.g.value(green)
   
   def update(self): 
      # state is 0:Purple, 1:Blue, 2:Green, 3:Red 
      pins = [(0,0,1), (1,0,1), (1,1,0), (0,1,1)]
      if (utime.ticks_ms() > self.blinkTimeout) or (self.lastState != self.state): 
         self.lastState = self.state
         self.blinkTimeout = utime.ticks_ms() + 500 
         self.blink = 1 - self.blink
         if self.blink == 0: 
            self.set (1,1,1)
         else:
            self.set (pins[self.state][0],pins[self.state][1],pins[self.state][2]) 
      





