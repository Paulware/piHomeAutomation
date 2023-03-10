# Note: This python program should be started from /etc/rc.local
from socket import *
import time
import select
import os
import re
import sys

UDP_PORT = 4444

def getLocalAddress ():
  ipAddress = '192.168.0.X'
  line = os.popen("/sbin/ifconfig eth0").read().strip()  
  p = re.findall ( r'[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+', line )
  if p: 
     ipAddress = p[0]   
     
  return ipAddress 
  
def getBroadcastAddress ():
  address = getLocalAddress()
  index = address.rfind ('.')
  addr = address[0:index] + '.255'
  return addr  
  
msg = sys.argv[1]  
startTime = time.time()
sock = socket(AF_INET, SOCK_DGRAM)
sock.bind (('',0))
sock.setsockopt (SOL_SOCKET, SO_BROADCAST, 1)
print 'Local Address: ' + getLocalAddress()
destination = getBroadcastAddress() # '192.168.0.255' # '<broadcast>'
sock.sendto(msg, (destination, UDP_PORT))
print 'Sent ' + msg + ' to ' + destination + ':' + str(UDP_PORT) + '\n'
startTime = time.time() + 30
   
print 'done'