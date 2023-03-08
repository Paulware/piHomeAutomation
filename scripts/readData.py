#!/usr/bin/env python

from __future__ import print_function
import serial
import time
import pygame
from pygame.locals import *
from threading import Thread

port = "/dev/serial0"
port = "/dev/ttyUSB0"
WINDOWWIDTH  = 480 
WINDOWHEIGHT = 320 

WHITE      = (255, 255, 255)
BLACK      = (  0,   0,   0)
GREEN      = (  0, 155,   0)
BLUE       = (  0,  50, 255)
BROWN      = (174,  94,   0)
RED        = (255,   0,   0)

ports = ["2","3","4","5","6","7","8","9","A","B","C","E","F","G","H","I"]
portIndex = 0;

TEXTBGCOLOR2 = GREEN
GRIDLINECOLOR = BLACK
TEXTCOLOR = WHITE

pygame.init()
BIGFONT = pygame.font.Font('freesansbold.ttf', 24)
MEDIUMFONT = pygame.font.Font('freesansbold.ttf', 20)
LITTLEFONT = pygame.font.Font('freesansbold.ttf', 16)
DISPLAYSURF = pygame.display.set_mode ((0, 0), pygame.FULLSCREEN) 

def sendLine(line, y, font):
   global DISPLAYSURF
   global pygame

   print (line)
   msgSurf = font.render(line, True, WHITE, BLUE)
   msgRect = msgSurf.get_rect()
   msgRect.topleft = (0, y)
   DISPLAYSURF.blit(msgSurf, msgRect)
   pygame.display.update()
   
def sendLines (lines):   
   global DISPLAYSURF
   global BIGFONT
   
   x = 0
   y = 10
   for line in lines:
      sendLine (line,y,MEDIUMFONT )
      y = y + 22
         
   pygame.display.update()


try:
   print ("Opening serial port" )
   serialPort = serial.Serial(port=port, baudrate=9600, timeout=0.1)
   print("Opened port", port, "for testing:")
   startTime = time.time() - 30 
   line = ""
   msg = []
   readyToClear = False
   while (True):
      ch = serialPort.read(1)
      if ch != "":
         if ch == chr(13):
            print ("Got line [" + line + "]" )
            msg.append (line)
            if line.find ( "done") > -1: 
               # View for 2 seconds and read the next port 
               startTime = time.time() - 30
               readyToClear = True
               # For testing only
               #DISPLAYSURF.fill((BLACK)) # Clear the screen 
               #msg = [] 
               
            elif line.find ( "," ) > -1:
               if readyToClear:             
                  DISPLAYSURF.fill((BLACK)) # Clear the screen 
                  readyToClear = False             
                  msg = []
            elif line.find ( "numDevices:0") > -1: 
               time.sleep (2)
               DISPLAYSURF.fill ((BLACK))
               msg = []               
            line = ""
            sendLines (msg)
         elif ch != chr(10):
            line = line + ch
            
      if time.time() > (startTime + 30):
         startTime = time.time()
         # Cause an Arduino reset 
         serialPort.close()
         serialPort = serial.Serial(port=port, baudrate=9600, timeout=0.1)
         time.sleep (3) # Let Arduino boot up                  
         serialPort.write ( "readport" + ports[portIndex] + "\n\r" )
         portIndex = portIndex + 1;
         portIndex = portIndex % 16;
         #print ("readport2" )

except IOError:
   print ("Failed at", port, "\n")
finally:
   serialPort.close()
