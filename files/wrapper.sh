#!/bin/bash

COMMAND="/usr/bin/php -q /home/jabber/bin/jabber/jabber.php"

a=`ps -fe | grep "$COMMAND" | grep -v grep`
if [ ! "$a" ]; then
    echo "No connection"
    $COMMAND 1> /dev/null 2> /dev/null &
    date >> /home/jabber/bin/jabber/resume.log
else
    echo "Connected"
fi