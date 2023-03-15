

# Connection
import network
import ubinascii
import machine
import ntptime
import utime
from Utilities import Utilities
from LED import LED

import usocket as socket
import ustruct as struct

class Connection:

    def __init__(self, ssid, password):
                 
        #self.print ( 'init Connection')        
        self.station = network.WLAN (network.STA_IF) 
        self.canConnectWifi = True
        self.ssid = ssid
        self.password = password 
        self.station.active(True) 
        self.led = LED()
        self.connectWifi()                 
        self.print ( 'Done init Connection')

    def print (self,msg,delay=0.5): 
        print (Utilities.now() + ' ' + msg)
        utime.sleep (delay)        

    def disconnect(self):
        self.print ( 'Connection disconnect')
        try: 
           self.station.disconnect()
        except Exception as ex:
           self.print ( 'Could not station.disconnect because: ' + str(ex))         
        
    def reset(self):
       Utilities.print ( 'Connection, resetting')   
       try: 
          self.disconnect()
          self.connectWifi()
          if not network.WLAN().isconnected():
             Utilities.print ( 'Connection .reset could not reacquire Wifi' )
             machine.reset ()
       except Exception as ex:
          Utilities.print ( 'Could not Connection.reset because: ' + str(ex)) 
          machine.reset()         
          
    def reset(self):
       Utilities.print ( 'Connection, resetting')   
       try: 
          self.disconnect()
          self.connectWifi()
          if not network.WLAN().isconnected():
             Utilities.print ( 'Connection .reset could not reacquire Wifi' )
             machine.reset ()
       except Exception as ex:
          Utilities.print ( 'Could not Connection.reset because: ' + str(ex)) 
          machine.reset()         
          
    def connectNTP (self): 
       Utilities.print ( 'connectNTP')
       endTime = utime.ticks_ms() + 30000 # try for 30 seconds to connect to ntp 
       while utime.ticks_ms() < endTime: 
          try: 
             Utilities.print ( 'Get ntptime' )
             ntptime.host = 'us.pool.ntp.org'
             ntptime.settime()
             Utilities.print ( self.ssid + ' connected: ' + str(self.station.ifconfig()))
             self.led.set (1,0,1) # blue
             break
          except Exception as ex:
             Utilities.print ( 'Trouble with ntptime: ' + str(ex) )
             self.led.set (0,1,1) # red
       Utilities.print ( 'Done in connectNTP')
                       
    def WLANreset(self):
       Utilities.print ( 'Connection, resetting')   
       try: 
          self.disconnect()
          self.connectWifi()
          if network.WLAN().isconnected():
             print ( 'WLAN is reset/connected' )
          else:
             Utilities.print ( 'Connection .reset could not reacquire Wifi' )
             machine.reset ()
       except Exception as ex:
          Utilities.print ( 'Could not Connection.reset because: ' + str(ex)) 
          machine.reset()         
                 
    def connectWifi (self): 
       connected = False
       Utilities.print ( 'Check if station is connected?' ) 
       try:  
          Utilities.print ( 'connect [ssid,password] : [' + self.ssid + ',' + self.password + ']')
          self.station.connect(self.ssid, self.password)
          Utilities.print ( 'Wait 120 seconds for station connected' )
          blink = True 
          for retry in range (120):
             blink = not blink
             if blink:
                self.led.set (0,1,1) # red
             else:
                self.led.set (1,1,1) # off 
             if self.station.isconnected ():
                connected = True 
                break
             else:
                Utilities.print ( 'No connection yet')   
             
          if connected: 
             Utilities.print ( self.ssid + ' is connected' )
          else:
             Utilities.print ( self.ssid + ' not connected, try again' )
             self.reset()
                                               
       except Exception as ex: 
          Utilities.print ( 'Connection issue: ' + str(ex))
          self.reset()
              
       self.connectNTP()   

        




