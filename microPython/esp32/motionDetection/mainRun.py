

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
   
print ( 'Motion sensor code V1.00')              
  
motion = False

def handle_interrupt(pin):
  global motion
  motion = True
  global interrupt_pin
  interrupt_pin = pin 
  
# Standalone objects
led = LED()
led.purple()
utilities = Utilities()

testing = False 
msgSent = False
#Change these to match your system   
if testing: 
   ssid = 'RICHARDS_WiFi-2.4G' # alternate Wifi
   password = 'mypassword'     # alternate password 
else:
   ssid     = 'pi4'   
   password = 'ABCD1234' 
# Note: If device is not logging into the pi's wifi, the server address will be different
serverAddress = '192.168.4.1' # pi's built in wifi 

interrupt_pin = 14
pir = Pin(interrupt_pin, Pin.IN)
pir.irq(trigger=Pin.IRQ_RISING, handler=handle_interrupt)

connection = Connection (ssid, password )
if network.WLAN().isconnected(): 
   led.blue()
   print ( 'mac: ' + connection.mac )
   print ( 'serverAddress: ' + serverAddress )
else:
   print ( 'Warning....I am not WLAN connected') 
   

print ( 'Running infinite sensor loop' )
while True:
  if network.WLAN().isconnected():  
     if testing and not msgSent: 
        motion = True
        
     if motion: 
        print('Motion detected! Interrupt caused by:', interrupt_pin)
        led.red()
        led.update()
        url = 'http://' + serverAddress + \
              '/Paulware/updateSensor.php?MAC=' + str(connection.mac) + \
              '&value=1' \
              ' HTTP/1.1\r\nHost: Paulware\r\nConnection: keep-alive\r\nAccept: */*\r\n\r\n'   
        msgSent = True               
        sleep(2)
        print('Motion stopped!')
        motion = False
        try: 
           r = urequests.get(url)
        except Exception as ex: 
           print ( 'Could not request url because:' + str(ex)) 
          
        print ( 'sensor updated ' + str(temperature)) 
     else:
         led.green()
         
           
  else: 
     led.red() 
     Utilities.print ( 'Wifi Connection was lost, reconnect')
     connection.reset()     
  led.update()
  
Utilities.print ( 'Done' )

