#!/usr/bin/env python
import sys

print str (sys.argv) 
if sys.argv.__len__() > 1: 
   try: 
      filename = 'xsend' # '/etc/sendxmpprc'
      f = open ( filename, 'w')
      #f.write  ( sys.argv[1] + ' ' + sys.argv[2] + '\n')
      line = 'jid=' + sys.argv[1] + '\n' + 'password=' + sys.argv[2]
      f.write  (line + '\n')
      f.close  ()
      print    ( filename + ' created' )
   except Exception as inst:
      print 'Could not create ' + filename + ' because: ' + str(inst)   
else:
   print 'Usage: \n  python makeJabberConfig.py jabberUsername@Account jabberPassword'