from socket import *
import time
import select
import os
import re
import sys

print (sys.argv)
ipAddress = sys.argv[1]
which = sys.argv[2]
loHi = sys.argv[3]
port = 3333
sock = socket(AF_INET, SOCK_DGRAM)
sock.bind (('',0))
sock.setsockopt (SOL_SOCKET, SO_BROADCAST, 1)
if loHi.lower() == 'hi':
   msg = 'turn on ' + which
else:
   msg = 'turn off' + which
   
sock.sendto(msg, (ipAddress, port))
print 'Sent ' + msg + ' to ' + ipAddress + ':' + str(port) + '\n'   
print 'done'
