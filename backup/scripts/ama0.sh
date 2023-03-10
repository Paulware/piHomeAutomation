if [ "$EUID" -ne 0 ]
	then echo "Must be root"
	exit
fi

file_exists() {
   if [ -e $1 ]
   then
       # file found 
       return 0
   else
       return 1
   fi
}

# return true if line exists
line_exists_in () {
   if grep -Fxq "$2" $1
   then
      return 0
   else
      return 1
   fi
}


if line_exists_in /boot/config.txt "enable_uart=1"
then
  echo " "
  echo ":) /boot/config.txt already has enable_uart=1"
else
  echo "setting enable_uart=1 in /boot/config.txt"
  echo "enable_uart=1" >> /boot/config.txt
fi
