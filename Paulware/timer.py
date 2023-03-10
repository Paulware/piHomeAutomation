import MySQLdb
import time

from smtplib import SMTP_SSL
import GmailConfig

stuff = {'name': 'Zed', 'age': 39, 'height': 6 * 12 + 2}

providers = { '3river'         : ("3 River Wireless", "sms.3rivers.net"),
              'acs'            : ("ACS Wireless", "paging.acswireless.com"),
              'alltel'         : ("Alltel","message.alltel.com"),
              'att'            : ("AT&T","txt.att.net"),     
              'bellcanada'     : ("Bell Canada","txt.bellmobility.ca"),     
              'bellmobilityca' : ("Bell Mobility (Canada)", "txt.bell.ca"),
              'bluesky'        : ("Blue Sky Frog", "blueskyfrog.com"),
              'boost'          : ("Boost Mobile","myboostmobile.com"),
              'cricket'        : ("Cricket","sms.mycricket.com"),
              'metro'          : ("Metro PCS","mymetropcs.com"),
              'nextel'         : ("Nextel","messaging.nextel.com"),
              'qwest'          : ("Qwest","qwestmp.com"), 
              'tmobile'        : ("T-Mobile","tmomail.net"),
              'sprintpcs'      : ("Sprint PCS","messaging.sprintpcs.com"),
              'sprintpm'       : ("Sprint PM","pm.sprint.com"),
              'suncom'         : ("Suncom", "tms.suncom.com"),
              'telus'          : ("Telus (Canada)","msg.telus.com"),
              'uscellular'     : ("US Cellular","email.uscc.net"),
              'verizon'        : ("Verizon","vtext.com"), 
              'virgin'         : ("Virgin Mobile","vmobl.com"),
              'vodafone'       : ("Vodafone (UK)","vodafone.net")
            }
  

db = MySQLdb.connect ( "localhost", "root", "pi", "Paulware")
curs = db.cursor()

try: 
   sql = "Select * from sensors Where MAC=\'timer\'"
   print "sensors:"
   curs.execute(sql)
   value = 0
   for reading in curs.fetchall():
      value = int(reading[2])
   print "\n***Done in sensors value: " + str(value)
   
   newValue = value + 1   
   if value == 10000:
      value = 0
   sql = "UPDATE sensors SET value=\'" + str(newValue) + "\' WHERE MAC=\'timer\'"
   print sql
   curs.execute (sql)
   sql = "Select * from sensors Where MAC=\'timer\'"
   curs.execute(sql)
   sensorId = 0
   for reading in curs.fetchall():
      print str(int(reading[0]))
      print str(reading)   
      sensorId = reading[0]
      print 'ID=' + str(sensorId)  
            
   if (sensorId == 0):  
      f = open ( '/var/www/Paulware/python.log', 'a')
      f.write  ( 'sensorId == 0, so doing nothing...Was timer deleted?\n')
      f.close  ()      
   else:
      sql = "Select * from actions Where Sensor=" + str(sensorId)
      curs.execute (sql)
      timeValue = 0
      for reading in curs.fetchall():
         print str(reading)
         timeValue   = reading[12]  # TODO: Change 5 to correct value
         action      = reading[3]   # Change to correct
         destination = reading[7]   # To Address for emails
         subject     = reading[9]
         body        = reading[10]
         provider    = reading[11]  
         phone       = reading[6]
         msg         = reading[8]
     
      currentTime = time.strftime ("%H:%M", time.localtime())
      isTriggered = False
      if timeValue == 0: 
         print 'No actions found for timer:'
      else:         
         if timeValue != currentTime:   
            print 'Take no action because currentTime (' + currentTime + ') != actionTime (' + timeValue + ')'   
         else:
            isTriggered = True
            print 'Take action now : [' + action + ']' 
         
         if isTriggered:    
            if action == 'text':
               gmailConfig = GmailConfig.GmailConfig()
               login, password = gmailConfig.login, gmailConfig.password       
            
               emailAddress = providers[provider][1]
               destination = phone + '@' + emailAddress
               subject = '';
               
               message = "From: richardspaulr1@gmail.com\n"  
               message = message + "To: " + destination + "\n"
               message = message + "Subject: " + msg.strip() + "\n" 
               message = message + "" # body
               
               print 'send this message: ' + message

               s = SMTP_SSL('smtp.gmail.com', 465, timeout=30)
               s.set_debuglevel(1)
               print message
               try:
                   s.login(login, password)
                   s.sendmail(login, destination, message)
               except Exception as inst:
                   print 'Exception: ' + str(inst)
               finally:
                   s.quit()

            elif action == 'email':
               gmailConfig = GmailConfig.GmailConfig()
               login, password = gmailConfig.login, gmailConfig.password 
                  
               message = "From: richardspaulr1@gmail.com\n"  
               message = message + "To: " + destination + "\n"
               message = message + "Subject: " + subject.strip() + "\n\n" 
               message = message + body + "\n"

               # send it via gmail
               s = SMTP_SSL('smtp.gmail.com', 465, timeout=30)
               s.set_debuglevel(1)
               print message
               try:
                   s.login(login, password)
                   s.sendmail(login, destination, message)
               except Exception as inst:
                   print 'Exception: ' + str(inst)
               finally:
                   s.quit()
            elif action == 'IM':
               print 'sendIM'
               # sendIM ($Phone, $Body)
   
   db.commit()
   print 'db changes committed'
except Exception as inst:
   f = open ( '/var/www/Paulware/python.log', 'a')
   f.write ( 'could not because: ' + str(inst) + '\n')
   f.close()   