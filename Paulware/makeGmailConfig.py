#!/usr/bin/env python
import sys

print str (sys.argv) 
if sys.argv.__len__() > 1: 
   try: 
      f = open ( '/var/www/html/Paulware/GmailConfig.py', 'w')
      f.write  ( 'class GmailConfig():\n')
      f.write  ( '   def __init__ (self):\n')
      f.write  ( '       self.login = \'' + sys.argv[1] + '\'\n' )
      f.write  ( '       self.password = \'' + sys.argv[2] + '\'\n')
      f.close  ()
      print    ( 'GmailConfig.py created' )
   except Exception as inst:
      print 'Could not create GmailConfig.py because: ' + str(inst)   
else:
   print 'Usage: \n  python makeGmailConfig.py username@gmail.com gmailPassword'