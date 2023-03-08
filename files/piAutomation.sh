#!/bin/bash

#------------------------------------------------------------------------------
function do_anykey ()
{
   echo ""
   echo "######################################"
   echo "#          Review Output             #"
   echo "######################################"
   read -p "  Press Enter to Return to Main Menu"
}

#------------------------------------------------------------------------------
function do_ssh ()
{
  if (whiptail --title "Enable ssh (you can also do this by raspi-config)" --yesno "" 8 65 --yes-button "Enable" --no-button "Cancel" ) then
    # permit ssh login  
    sudo touch /boot/ssh
    # permit root ssh login
    update_file /etc/ssh/sshd_config "#PermitRootLogin prohibit-password" "PermitRootLogin yes"
    sudo echo "root:raspberry" | sudo chpasswd
    sudo echo "pi:goaway" | sudo chpasswd
    echo "ssh is enabled"
    echo "This works as long as you have root access..."
    do_anykey
  fi
}

#---
function do_lamp() 
{
    sudo apt install apache2 -y
    cd /var/www/html
    ls -al
    hostname -I
    sudo apt install php -y
    sudo rm index.html
    sudo echo "<?php echo \"hello world\"; ?>" > index.php
    cat index.php
    sudo service apache2 restart
    sudo apt install mariadb-server php-mysql -yes
    sudo servive apache2 restart
}

#------------------------------------------------------------------------------
function do_main_menu ()
{
  SELECTION=$(whiptail --title "Pi4 WOD Main Menu" --menu "Arrow/Enter Selects or Tab Key" 0 0 0 --cancel-button Quit --ok-button Select \
  "b SSH" "Enable SSH" \
  "z QUIT" "Exit menubox.sh" 3>&1 1>&2 2>&3)
  

  RET=$?
  if [ $RET -eq 1 ]; then
    exit 0
  elif [ $RET -eq 0 ]; then
    case "$SELECTION" in
      b\ *) do_ssh ;; 
      z\ *) clear
            exit 0 ;;
         *) whiptail --msgbox "Programmer error: unrecognized option" 20 60 1 ;;
    esac || whiptail --msgbox "There was an error running selection $SELECTION" 20 60 1
  fi
  
}

#------------------------------------------------------------------------------
#                                Main Script
#------------------------------------------------------------------------------
if [ $# -eq 0 ] ; then
  while true; do
     do_main_menu
  done
fi