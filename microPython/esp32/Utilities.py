

import time 
import utime
import os
class Utilities ():

   @staticmethod
   def now ():
      stamp = "YYYY/MM/DD HH:MM:SS"
      try: 
         t = time.mktime (time.gmtime())
         t -= 6*3600
         data = time.localtime(t)
         month = str(data[1]) 
         if data[1] < 10:
            month = '0' + month 
         day = str(data[2]) 
         if data[2] < 10:
            day = '0' + day
         hour = str(data[3]) 
         if data[3] < 10: 
            hour = '0' + hour    
         minute = str(data[4]) 
         if data[4] < 10:
            minute = '0' + minute 
         second = str(data[5]) 
         if data[5] < 10:
            second = '0' + second 
         year = data[0]
         if year > 1999: 
            year = str(year) 
   
            stamp = year + '/' + month + '/' + day + ' ' + \
                    hour + ':' + minute + ':' + second
         else:
            stamp = ''
      except Exception as ex:
         print ( 'timestamp error: ' + str(ex)) 
         
      return stamp 
      
   @staticmethod
   def fileExists (filename): 
      return filename in os.listdir()   
      
   @staticmethod
   def print (message, delay = 0.5):
      print (Utilities.now() + ' ' + message)
      utime.sleep (delay)

   @staticmethod
   def insideChar (char, line): 
      capture = False 
      msg = ''
      for i in range (len(line)): 
         ch = line[i:i+1]
         if ch == char:
            capture = not capture
         else:
            if capture: 
               msg = msg + ch           
      return msg
      
   @staticmethod
   def findNumber (line): 
      value = ''      
      val = '-1' 
      for i in range(len(line)): 
         ch = line [i:i+1] 
         if ch in ['0','1','2','3','4','5','6','7','8','9','.']:
            value = value + ch
            val = value            
         else:
            value = ''           
      # print ( 'findNumber found: [' + val + ']')
      return val
      
   def timestamp(self): 
      stamp = "YYYY/MM/DD HH:MM:SS"
      try: 
         t = time.mktime (time.gmtime())
         t -= 6*3600
         data = time.localtime(t)
         month = str(data[1]) 
         if data[1] < 10:
            month = '0' + month 
         day = str(data[2]) 
         if data[2] < 10:
            day = '0' + day
         hour = str(data[3]) 
         if data[3] < 10: 
            hour = '0' + hour    
         minute = str(data[4]) 
         if data[4] < 10:
            minute = '0' + minute 
         second = str(data[5]) 
         if data[5] < 10:
            second = '0' + second 
         year = data[0]
         # year = year - 2000 

         year = str(year) 
   
         stamp = year + '/' + month + '/' + day + ' ' + \
                 hour + ':' + minute + ':' + second
      except Exception as ex:
         print ( 'timestamp error: ' + str(ex))      
      return stamp            
  
   def timestmp(self): # Shorten timestamp for display on OLED
      stamp = self.timestamp()
      stamp = stamp [5:]
      return stamp   


