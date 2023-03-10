import sys
import urllib
import GmailConfig
import smtplib

print '<br><h1>sendMail.py</h1><br>\n'
print '<h2>sys.argv:</h2><br>\n' 
print str(sys.argv) + '<p>\n' 

Username = "paulware@hotmail.com"
Subject = "Default Subject 2"
Body = "Default Body"

if sys.argv.__len__() > 1: 
    Username = sys.argv[1]
    Subject = sys.argv[2]
    Body = sys.argv[3]

gmailConfig = GmailConfig.GmailConfig()
login, password = gmailConfig.login, gmailConfig.password 
recipients = [Username]
   
message = "From: richardspaulr1@gmail.com\n"  
message = message + "To: " + Username + "\n"
message = message + "Subject: " + Subject.strip() + "\n" 
message = message + Body

# send it via gmail
print message
server = smtplib.SMTP ( 'smtp.gmail.com', 587)
try:
    server.starttls()
    print 'login,password: [' + login + ',' + password + ']'
    server.login(login, password)
    print 'recipients, message: [' + str(recipients) + ',' + message + ']'
    server.sendmail(login, recipients, message)
except Exception as inst:
    print 'Exception: ' + str(inst)
finally:
    server.quit()