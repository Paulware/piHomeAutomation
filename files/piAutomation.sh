#!/bin/bash

#------------------------------------------------------------------------------
# update_file filename "Find String" "Replace String"
function update_file() {
  cat $1 | sed -e "s/$2/$3/" > /f
  mv /f $1
}

#------------------------------------------------------------------------------
function accessPoint ()
{
  if (whiptail --title "set wireless access point" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
     read -p "Enter SSID name for the access point" SSID
  
     apt-get install dnsmasq hostapd -y

     echo "turn off services"
     systemctl stop dnsmasq
     systemctl stop hostapd

     echo "modify /etc/dhcpcd.conf" 
     if line_exists_in /etc/dhcpcd.conf "interface wlan0"
     then
       echo " "
       echo ":) /etc/dhcpcd.conf already has interface wlan0"
     else
       echo "setting static ip_address in /etc/dhchpcd.conf"
       echo "interface wlan0" >> /etc/dhcpcd.conf
       echo "   static ip_address=192.168.4.1/24" >> /etc/dhcpcd.conf
       echo "   nohook wpa_supplicant" >> /etc/dhcpcd.conf

     fi

     # Modify /etc/dnsmasq.conf" 
     mv /etc/dnsmasq.conf /etc/dnsmasq.conf.orig
     echo "interface=wlan0" > /etc/dnsmasq.conf
     echo "  dhcp-range=192.168.4.2,192.168.4.20,255.255.255.0,24h" >> /etc/dnsmasq.conf

     # Modify /etc/hostapd/hostapd.conf to create the ap1 access point
     cat > /etc/hostapd/hostapd.conf <<EOF
interface=wlan0
driver=nl80211
ssid=$SSID
hw_mode=g
channel=7
wmm_enabled=0
macaddr_acl=0
auth_algs=1
ignore_broadcast_ssid=0
wpa=2
wpa_passphrase=ABCD1234
wpa_key_mgmt=WPA-PSK
wpa_pairwise=TKIP
rsn_pairwise=CCMP

EOF


     # Modify hostapd.conf
     sed -i -- 's/#DAEMON_CONF="\/etc\/hostapd\/hostapd.conf"/DAEMON_CONF="\/etc\/hostapd\/hostapd.conf"/g' /etc/default/hostapd
     sed -i -- 's/#DAEMON_CONF=""/DAEMON_CONF="\/etc\/hostapd\/hostapd.conf"/g' /etc/default/hostapd

     echo "Restart hostapd/dnsmasq" 
     systemctl unmask hostapd
     systemctl enable hostapd
     systemctl start hostapd
     systemctl start dnsmasq
     rfkill unblock 0
     ifconfig wlan0 up

     echo "$SSID with password ABCD1234 should now appear"

     pause
  fi
}



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
function copy_directories ()
{
  if (whiptail --title "Make and copy directories" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then
    echo "#Copy directories"
    cd /boot
    cp -R Paulware /var/www/html
    cd /var/www/html
    chmod +x *.*
    echo "Directories copied...."   
    pause
  fi
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

function extraYo() 
{
   -- sudo mysql -uroot -p
   -- create database Paulware;
   -- GRANT ALL PRIVILEGES ON Paulware.* TO 'root'@'localhost' IDENTIFIED BY '';
   -- FLUSH PRIVILEGES;
   -- Cntl-D
   -- reboot
   
   -- create user admin@localhost identified by '';
   -- grant all privileges on *.* to admin@localhost;
   -- FLUSH PRIVILEGES
   -- exit;
   
   -- sudo apt install phpmyadmin -y
   
   -- PHPMYAdmin installation program will ask you a few questions: 
   -- Select Apache2 when prompted and press the Enter key
   -- Configuring phpmyadmin? OK
   -- Configure database for phpmyadmin with dbconfig-common? Yes
   -- Type your password (raspberry) and press OK
   
   -- sudo phpenmod mysqli
   -- sudo service apache2 restart
   
   -- ln -s /usr/share/phpadmin /var/www/html/phpmyadmin
   
   -- mysql
   -- ALTER USER 'root'@'localhost' IDENTIFIED BY 'raspberry'
   
   -- http://192.168.4.1/phpmyadmin/index.php
   -- root
   -- raspberry
   
   -- Necessary?
   -- change privileges
   -- ls -h /var/www/
   -- sudo chown -R pi:www-data /var/www/html/
   -- sudo chmod -R 770 /var/www/html/
   -- ls -lh /var/www/
   
   -- Necessary? 
   -- /etc/php/7.4/apache2
   -- extension=msqli.so
   -- extension=msql.so
}

#------------------------------------------------------------------------------
function do_lamp() 
{
    sudo apt-get update
    sudo apt install apache2 -y
    cd /var/www/html
    ls -al
    hostname -I
    sudo apt install php -y
    sudo rm index.html
    sudo echo "<?php echo \"hello world\"; ?>" > index.php
    cat index.php
    sudo service apache2 restart
    sudo apt-get install mariadb-server php-mysql -y
    sudo service apache2 restart
}

#------------------------------------------------------------------------------
function do_main_menu ()
{
  SELECTION=$(whiptail --title "Pi4 WOD Main Menu" --menu "Arrow/Enter Selects or Tab Key" 0 0 0 --cancel-button Quit --ok-button Select \
  "a WIFI AP" "Wireless AccessPoint install" \
  "b SSH" "Enable SSH" \
  "c cp dirs" "Copy Paulware directory" \
  "l LAMP" "Install Apache, SQL, PHP" \
  "z QUIT" "Exit menubox.sh" 3>&1 1>&2 2>&3)
  

  RET=$?
  if [ $RET -eq 1 ]; then
    exit 0
  elif [ $RET -eq 0 ]; then
    case "$SELECTION" in
      a\ *) accessPoint ;; 
      b\ *) do_ssh ;; 
      c\ *) copy_directories ;;
      l\ *) do_lamp ;; 
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