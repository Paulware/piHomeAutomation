# Items user can change 
pinInput = 4
#Change these to match your system (if you don't want to use the pi's wifi)  
ssid     = 'pi4'   
password = 'ABCD1234' 
# Note: If ssid is changed, you need to know the server address and change the next line 
serverAddress = '192.168.4.1' # pi's built in wifi 
timeout = 60000 # Number of milliseconds between reports

testing  = False 
#  imports 
print ( 'imports' )
try: 
   import machine
   from time import sleep
   import network
   from Connection import Connection 
   import utime
   from Utilities import Utilities
   from LED import LED
   import urequests
   import onewire, ds18x20

except Exception as ex:
   print ( 'Could not import because: ' + str(ex)) 

ds_pin = machine.Pin(pinInput)
ds_sensor = ds18x20.DS18X20(onewire.OneWire(ds_pin))
roms = ds_sensor.scan()
if len(roms) == 0: 
   print ( 'Found no devices!')
else:
   print('Found DS devices: ', str(roms) )  
      
# pre-defined functions
def sendMessage (value, testing):
   if testing:        
      mac = '1234'
   else:
      mac = connection.mac 
      
   Utilities.print ( 'mac: ' + mac )
   Utilities.print ( 'serverAddress: ' + serverAddress )
   Utilities.print ( 'value: ' + str(value) )
      
   url = 'http://' + serverAddress + \
         '/Paulware/updateSensor.php?MAC=' + mac + \
         '&value=' + str(value) + ' HTTP/1.1\r\nHost: Paulware\r\nConnection: keep-alive\r\nAccept: */*\r\n\r\n'   
   try: 
      if not testing: 
         urequests.get(url)
   except Exception as ex: 
      Utilities.print ( 'Could not request url because:' + str(ex))       
           
Utilities.print ( 'DS18B20 sensor code V1.01')              

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
             
Utilities.print ( 'Running infinite sensor loop' )
timer = 0

utime.ticks_ms() + 60000
value = 98.6
while True:
   if utime.ticks_ms() > timer:       
      timer = utime.ticks_ms() + timeout
      
      try: 
         ds_sensor.convert_temp()         
         for rom in roms:
            print(rom)
            value = ds_sensor.read_temp(rom)
      except Exception as ex:
         print ( 'One wire error: ' + str(ex) ) 
         value = value + 0.1
         
      if value > 98.8: 
         led.red()
      else:
         led.green()      
      sendMessage (value, testing)
         
      if not testing:   
         if not network.WLAN().isconnected():
            led.red() 
            Utilities.print ( 'Wifi Connection was lost, reconnect')
            connection.reset() 
            
   led.update()
   
Utilities.print ( 'Done' )
