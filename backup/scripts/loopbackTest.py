#!/usr/bin/env python
# serial_port_loopback.py
# Will also work on Python3.
# Serial port testing for a RaspberryPi.

from __future__ import print_function
import serial

test_string = "Testing 1 2 3 4".encode('utf-8')
#test_string = b"Testing 1 2 3 4" ### Will also work

port_list = ["/dev/ttyS0", "/dev/serial0", "/dev/ttyAMA0", "/dev/serial1"]

for port in port_list:

    try:
        serialPort = serial.Serial(port=port, baudrate=9600, \
                     bytesize=serial.SEVENBITS,  stopbits=serial.STOPBITS_ONE, \
                     timeout=2, parity=serial.PARITY_EVEN)
        serialPort.flushInput()
        serialPort.flushOutput()
        print("Opened port", port, "for testing:")
        bytes_sent = serialPort.write(test_string)
        print ("Sent", bytes_sent, "bytes")
        loopback = serialPort.read(bytes_sent)
        if loopback == test_string:
            print ("Received", len(loopback), "valid bytes, Serial port", port, "working \n")
        else:
            print ("Received incorrect data", loopback, "over Serial port", port, "loopback\n")
        serialPort.close()
    except IOError:
        print ("Failed at", port, "\n")
