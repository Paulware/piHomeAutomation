#!/bin/bash

ver="1.03"

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR

pyconfigfile="./settings.py"
filename_conf="./settings.conf"
filename_temp="./settings.conf.temp"

# return true if line_exists_in (filename, "Line")
#if line_exists_in /etc/dhcpcd.conf "interface wlan0"
#then
#else
#fi
line_exists_in () {
   if grep -Fxq "$2" $1
   then
      return 0
   else
      return 1
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

# update_file filename "Find String" "Replace String"
function update_file() {
  cat $1 | sed -e "s/$2/$3/" > /f
  mv /f $1
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

#------------------------------------------------------------------------------
function do_keyboard ()
{
  if (whiptail --title "Enable US Keyboard layout" --yesno "" 8 65 --yes-button "Enable" --no-button "Cancel" ) then
    # us keyboard 
    update_file /etc/default/keyboard "XKBLAYOUT=\"gb\"" "XKBLAYOUT=\"us\""

    echo "Keyboard set to us.  This will be affected on next reboot"   
    do_anykey
  fi
}

#------------------------------------------------------------------------------
function installConnectWise ()
{
  echo "You will need the ConnectWiseControl.ClientSetup.deb file"
  if (whiptail --title "Install connect wise" --yesno "" 8 65 --yes-button "Enable" --no-button "Cancel" ) then
    dpkg -i /boot/ConnectWiseControl.ClientSetup.deb
    echo "Connect Wise setup" 
    pause
  fi
}

#------------------------------------------------------------------------------
function do_reboot ()
{
  if (whiptail --title "Do reboot" --yesno "" 8 65 --yes-button "Reboot" --no-button "Cancel" ) then
    reboot

    echo "rebooting...."   
    do_anykey
  fi
}

function pause(){
 read -s -n 1 -p "Press any key to continue . . ."
 echo ""
}

#------------------------------------------------------------------------------
function do_directories ()
{
  if (whiptail --title "Make and copy directories" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then
    #Make directories 
    cd /boot
    mkdir -m 0777 /vrrx
    chmod 0777 /vrrx
    mkdir -m 0777 /share
    chmod 777 /share       
    mkdir /share/Raven
    chmod 777 /share/Raven
    mkdir /share/Raven/rxMaps
    chmod 777 /share/Raven/rxMaps
    
    echo "#Copy directories"
    cd /boot
    cp -R xsp /home/
    cd /home/xsp
    chmod +x *.*
    echo "Copy WOD to /share/WOD"
    cd /boot
    cp -R WOD /share
    cd /share/WOD
    chmod +x *.py
    cp cmd.py /share/cmd.py
    chmod 777 /share/WOD
    cd /boot
    cp -R xsp /home/
    cd /home/xsp/bin
    chmod +x *.*
    echo "Directories copied...."   
    pause
  fi
}

#------------------------------------------------------------------------------
function addBridge ()
{
  if (whiptail --title "Bridge the internet" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    DEBIAN_FRONTEND=noninteractive apt install -y netfilter-persistent iptables-persistent
    echo "net.ipv4.ip_forward=1" > /etc/sysctl.d/routed-ap.conf
    iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
    netfilter-persistent save
    echo "domain=wlan     # Local wireless DNS domain" >> /etc/dnsmasq.conf
    echo "address=/gw.wlan/192.168.4.1" >> /etc/dnsmasq.conf
    echo "country_code=US" >> /etc/hostapd/hostapd.conf
    pause
  fi
}

#------------------------------------------------------------------------------
function do_timezone ()
{
  if (whiptail --title "Set timezone to Chicago" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    timedatectl set-timezone America/Chicago
    echo "Timezone set to Chicago"   
    pause
  fi
}

#------------------------------------------------------------------------------
function installPynmea2 ()
{
  if (whiptail --title "Configure python to decode nmea" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    pip install pynmea2
    echo "pynmea installed"   
    pause
  fi
}

#------------------------------------------------------------------------------
function do_backdoor ()
{
  if (whiptail --title "Install backdoor AS400 command of raspberry pi" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    cat > /lib/systemd/system/backdoor.service <<EOF
#filename:  /lib/systemd/system/backdoor.service
[Unit]
Description=Start Backdoor

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /share/WOD;/usr/bin/python2.7 sshBackdoor.py > /share/WOD/sshBackdoor.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/backdoor.service
    systemctl daemon-reload
    systemctl enable backdoor.service
    # remove the autorun option from the file manager
    #sed -i 's/autorun=1/autorun=0/' /home/pi/.config/pcmanfm/LXDE-pi/pcmanfm.conf
    echo "On reboot the service should start see /share/WOD/sshBackdoor.log for details"
    echo "backdoor installed"   
    pause
  fi
}


#------------------------------------------------------------------------------
function ipService ()
{
  if (whiptail --title "Install service that displays ip Address" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    cat > /lib/systemd/system/displayIp.service <<EOF
#filename:  /lib/systemd/system/displayIp.service
[Unit]
Description=Display the ip address using pygame

[Service]
ExecStartPre=-/bin/sleep 25
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/rtk;/usr/bin/python2.7 ipAddress.py > /home/rtk/ipAddress.log 2>&1'
Restart=never
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/displayIp.service
    systemctl daemon-reload
    systemctl enable displayIp.service

    echo "On reboot the ip address of the wlan should be displayed"
    pause
  fi
}

#------------------------------------------------------------------------------
function monitorIpService ()
{
  if (whiptail --title "Install service that will reboot if ip Address is lost" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    cat > /lib/systemd/system/monitorIp.service <<EOF
#filename:  /lib/systemd/system/monitorIp.service
[Unit]
Description=Monitor the ip address and reboot if lost

[Service]
ExecStartPre=-/bin/sleep 25
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/rtk;/usr/bin/python2.7 monitorIpAddress.py > /home/rtk/monitorIpAddress.log 2>&1'
Restart=never
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/monitorIp.service
    systemctl daemon-reload
    systemctl enable monitorIp.service

    echo "On reboot the ip address of the eth0 will be monitored"
    pause
  fi
}


#------------------------------------------------------------------------------
function iaRtnService ()
{
  if (whiptail --title "Install service that gets real time correction from ia rtn" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    cat > /lib/systemd/system/iaRtn.service <<EOF
#filename:  /lib/systemd/system/aiRtn.service
[Unit]
Description=Get rtk corrections from ia rtn and push on ttyUSB0

[Service]
ExecStartPre=-/bin/sleep 25
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/rtk;/usr/bin/python3 iaRtn.py > /home/rtk/iaRtn.log 2>&1'
Restart=always
RestartSec=1s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/iaRtn.service
    systemctl daemon-reload
    systemctl enable iaRtn.service
    echo "On reboot the iown rtn service will run"
    echo "Note: You need to copy rtk directory to /home/rtk"
    pause
  fi
}
#------------------------------------------------------------------------------
function readNmeaService ()
{
  if (whiptail --title "Install service that parses gps" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    cat > /lib/systemd/system/readNmea.service <<EOF
#filename:  /lib/systemd/system/readNmea.service
[Unit]
Description=Parse GPS stream and update locations

[Service]
ExecStartPre=-/bin/sleep 25
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/rtk;/usr/bin/python2.7 readNmea.py > /home/rtk/readNmea.log 2>&1'
Restart=always
RestartSec=5s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/readNmea.service
    systemctl daemon-reload
    systemctl enable readNmea.service
    

    echo "On reboot the iown rtn service and gps parse will run"
    pause
  fi
}

#------------------------------------------------------------------------------
function pyWebService ()
{
  if (whiptail --title "Install python web service that allows user configuration of iartn" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    cat > /lib/systemd/system/pyWebServer.service <<EOF
#filename:  /lib/systemd/system/pyWebServer.service
[Unit]
Description=Start a python web-server that lets the user configuration the ia rtn interface

[Service]
ExecStartPre=-/bin/sleep 25
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/webServer;/usr/bin/python3 httpServer.py > /home/webServer/httpServer.log 2>&1'
Restart=always
RestartSec=5s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/pyWebServer.service
    systemctl daemon-reload
    systemctl enable pyWebServer.service
    

    echo "On reboot the iown rtn service and gps parse will run"
    pause
  fi
}

#------------------------------------------------------------------------------
function do_35display ()
{
  if (whiptail --title "Configure for 3.5 inch display (will cause reboot)" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    # Install 3.5 tft  
    cd /home/
    sudo rm -rf LCD-show
    git clone https://github.com/goodtft/LCD-show.git
    chmod -R 755 LCD-show
    cd LCD-show/
    #Note this script should only run once....More than once will add extra ":rotate=90" to /boot/config.txt
    #sed -i 's/dtoverlay=tft9341/dtoverlay=tft9341:rotate=90/' /boot/config.txt
    echo "dtoverlay=tft9341:rotate=90">>/boot/config.txt
    # next command will reboot the display
    sudo ./LCD35-show
    # sed -i 's/Option  "SwapAxes"      "1"/Option  "SwapAxes"      "0"/' /etc/X11/xorg.conf.d/99-calibration.conf
    apt-get update
    apt --fix-broken install
    echo "Next command will cause a reboot" 
    ./LCD35-show 0
    echo "Configured for 3.5 inch display"   
    pause
  fi
}

#------------------------------------------------------------------------------
function do_usbdrive ()
{
  if (whiptail --title "Configure device to respond to usb drives" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    apt-get install usbmount -y
    sed -i 's/MountFlags=slave/MountFlags=shared/' /lib/systemd/system/systemd-udevd.service
    echo "Configured to detect usb drives"   
    pause
  fi
}

#------------------------------------------------------------------------------
function do_piupdue ()
{
  if (whiptail --title "install piupdue" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    pip install piupdue
    echo "Installed piupdue, piupdue directory is already part of /share/WOD directory"   
    pause
  fi
}

#------------------------------------------------------------------------------
function wifiLogin()
{
  if (whiptail --title "Login to a wifi network/hotspot" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    read -p "Enter the SSID" SSID  
    read -p "Enter the password" PASSWORD
    echo "network={">>/etc/wpa_supplicant/wpa_supplicant.conf
    echo "   ssid=\"$SSID\"">>/etc/wpa_supplicant/wpa_supplicant.conf    
    echo "   psk=\"$PASSWORD\"">>/etc/wpa_supplicant/wpa_supplicant.conf
    echo "}">>/etc/wpa_supplicant/wpa_supplicant.conf
    echo "$SSID configured with password: $PASSWORD"
    pause
  fi
}

#------------------------------------------------------------------------------
function do_static()
{
  if (whiptail --title "Set the static ip address" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
    read -p "Enter physical layer (eth0/wlan0)" PHYSICALLAYER  
    read -p "Enter static ip address (router is at 192.168.0.1)" IPADDRESS
    echo $IPADDRESS
    cat >> /etc/dhcpcd.conf <<EOF
interface=$PHYSICALLAYER
 static ip_address=$IPADDRESS/24
 static routers=192.168.0.1

EOF

    echo "/etc/dhcpcd.conf updated $PHYSICALLAYER with static ip address $IPADDRESS"  
    echo "If setting up wlan0, you need to set the wireless country"
    pause
  fi
}

#------------------------------------------------------------------------------
function hapStatic()
{
  if (whiptail --title "Set static ip address to 10.1.8.X" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
     read -p "Enter static ip address (10.1.8.X)" IPADDRESS  
     sed -i 's/#static ip_address.*//' /etc/dhcpcd.conf
     if grep -q "static ip_address" /etc/dhcpcd.conf
     then
        echo "Using sed to update static ip address..."
        sed -i 's/static ip_address.*/static ip_address='"$IPADDRESS"'\/24/' /etc/dhcpcd.conf
     else
        cat >> /etc/dhcpcd.conf <<EOF
interface=eth0
 static ip_address=$IPADDRESS/24
 static routers=10.1.8.254
 static domain_name_servers=10.1.254.1 10.1.244.1
 dns-nameservers 10.1.254.1 10.1.244.1

EOF

        echo "/etc/dhcpcd.conf updated with static ip $IPADDRESS"
     fi

     echo "source-directory /etc/network/interfaces.d" > /etc/network/interfaces
     echo "Static IP address updated "   
     pause
  fi
}

# return true if line exists
function line_exists_in () {
   if grep -Fxq "$2" $1
   then
      return 0
   else
      return 1
   fi
}

#------------------------------------------------------------------------------
function do_update ()
{
  if (whiptail --title "apt-get update" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    apt-get update
    echo "update completed"   
    pause
  fi
}

#------------------------------------------------------------------------------
function makeEtcObdcinst ()
{
echo "[PostgreSQL]" > /etc/odbcinst.ini
echo "Description     = ODBC for PostgreSQL" >> /etc/odbcinst.ini
echo "Driver          = /usr/lib/arm-linux-gnueabihf/odbc/psqlodbcw.so" >> /etc/odbcinst.ini
echo "Setup           = /usr/lib/arm-linux-gnueabihf/odbc/libodbcpsqlS.so" >> /etc/odbcinst.ini
echo "FileUsage       = 1" >> /etc/odbcinst.ini
echo "" >> /etc/odbcinst.ini
echo "[PostgreSQL ANSI]" >> /etc/odbcinst.ini
echo "Description=PostgreSQL ODBC driver (ANSI version)" >> /etc/odbcinst.ini
echo "Driver=psqlodbcw.so" >> /etc/odbcinst.ini
echo "Setup=libodbcpsqlS.so" >> /etc/odbcinst.ini
echo "Debug=0" >> /etc/odbcinst.ini
echo "CommLog=1" >> /etc/odbcinst.ini
echo "UsageCount=1" >> /etc/odbcinst.ini
echo "" >> /etc/odbcinst.ini
echo "[PostgreSQL Unicode]" >> /etc/odbcinst.ini
echo "Description=PostgreSQL ODBC driver (Unicode version)" >> /etc/odbcinst.ini
echo "Driver=psqlodbcw.so" >> /etc/odbcinst.ini
echo "Setup=libodbcpsqlS.so" >> /etc/odbcinst.ini
echo "Debug=0" >> /etc/odbcinst.ini
echo "CommLog=1" >> /etc/odbcinst.ini
echo "UsageCount=1" >> /etc/odbcinst.ini
pause
echo "/etc/odbcinst.ini created "
}

#------------------------------------------------------------------------------
function installPostgres ()
{
  if (whiptail --title "install postgres" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    apt-get -y install postgresql-9.6 postgresql-server-dev-9.6 postgresql-client postgresql-client-common
    apt-get -y install unixodbc unixodbc-dev odbc-postgresql pgadmin3
    apt-get -y install postgresql-contrib

    # To avoid PostgreSQL file not found
    #cp /boot/odbcinst.ini /etc/.
    makeEtcObdcinst

    cd /etc/postgresql/
    cd "$(dirname "$(find -name pg_hba.conf)")"
    sed -i 's/#listen_addresses.*/listen_addresses = '\''*'\''/' postgresql.conf
    # chmod 777 pg_hba.conf
    sed -i 's/peer/trust/g' pg_hba.conf
    sed -i 's/md5/trust/g' pg_hba.conf
    sed -i 's/127.0.0.1\/32/0.0.0.0\/0/' pg_hba.conf
    sed -i 's/::1\/128/::\/0/' pg_hba.conf
    /etc/init.d/postgresql restart
  fi
}

#------------------------------------------------------------------------------
function hapInstall ()
{
  echo " You will need 3 directories located on the /boot drive:"
  echo "heartland - Which has the MakeTables.exe"
  echo "xsp - Which has the web-server" 
  echo "share - Which has the ReadMoistureBins.exe"
  
  if (whiptail --title "install hap system" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    apt-get update
    installMono 
    do_samba
    installPostgres
    apt-get -y install dnsutils
    
    #directories      
    rm -rf /home/heartland
    cp -r /boot/heartland /home/heartland
    cd /home/heartland
    mono MakeTables.exe
    cd /boot
    rm -rf /home/xsp
    cp -r /boot/xsp /home/xsp
    
    do_ssh 
    do_keyboard 
    
    #crontab
    rm /boot/mycron
    echo "0 7 * * * cd /boot;sudo ./rmLogs.sh" >> /boot/mycron
    echo "0 0 * * * cd /boot;sudo ./rmCsvs.sh" >> /boot/mycron
    echo "0 3 * * * /sbin/shutdown -r + 5" >> /boot/mycron
    echo "@reboot touch /boot/rebooting" >> /boot/mycron
    echo "Current crontab settings:"
    cat /boot/mycron
    crontab mycron
    rm /boot/mycron    
    
    installConnectWise
    
    do_timezone
    hapStatic
    cat >> /home/pi/.bashrc <<EOF
cd /home/xsp;sudo mono bin/xsp4.exe --port 5050

EOF
    echo "xsp4 will now run on startup"   
    
    echo "Change /home/xsp/docs/doc.xml to match the correct site"
    echo "hap install completed"   
    pause
  fi
}


#------------------------------------------------------------------------------
function do_shutdown ()
{
  if (whiptail --title "shutdown -h now" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    shutdown -h now
    echo "shutdown completed"   
    pause
  fi
}

#------------------------------------------------------------------------------
function streamCamera ()
{
  if (whiptail --title "Install the camera service to run on startup" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat > /lib/systemd/system/camera.service <<EOF
#filename:  /lib/systemd/system/camera.service
[Unit]
Description=Start camera streaming program

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/camera;/usr/bin/python3 streamCamera.py > /home/camera/streamCamera.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/camera.service
    systemctl daemon-reload
    systemctl enable camera.service
    echo "On reboot the camera service should start see /home/camera/sreamCamera.log for details"
    pause
  fi
}



#------------------------------------------------------------------------------
function do_rtk_service ()
{
  if (whiptail --title "Install the rtk service to run python on startup and xsp webserver" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat > /lib/systemd/system/rtk.service <<EOF
#filename:  /lib/systemd/system/rtk.service
[Unit]
Description=Start rtk program

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/rtk;/usr/bin/python2.7 rtk.py > /home/rtk/rtk.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/rtk.service
    systemctl daemon-reload
    systemctl enable rtk.service
    
    cat >> /home/pi/.bashrc <<EOF
cd /home/xsp;sudo mono bin/xsp4.exe --port 80

EOF
    
    
    echo "On reboot the service should start see /home/rtk/rtk.log for details"
    echo "rtk service install complete"   
    pause
  fi
}

# Run the tool that get iartn corrections 
#------------------------------------------------------------------------------
function do_rtkPi_service ()
{
  if (whiptail --title "Install the rtk service to connect to ia rtn on startup and start xsp webserver" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat > /lib/systemd/system/rtk.service <<EOF
#filename:  /lib/systemd/system/rtk.service
[Unit]
Description=Start rtk program

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/rtk;/usr/bin/python2.7 iaRtn.py > /home/rtk/iaRtn.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/rtk.service
    systemctl daemon-reload
    systemctl enable rtk.service
    
    cat >> /home/pi/.bashrc <<EOF
cd /home/xsp;sudo mono bin/xsp4.exe --port 80

EOF
    
    
    echo "On reboot the service should start see /home/rtk/rtk.log for details"
    echo "rtk service install complete"   
    pause
  fi
}



#------------------------------------------------------------------------------
function do_boundary_service ()
{
  if (whiptail --title "Install the boundary service to run on startup" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat > /lib/systemd/system/boundary.service <<EOF
#filename:  /lib/systemd/system/boundary.service
[Unit]
Description=Start Boundary program

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/boundary;/usr/bin/python2.7 boundary.py > /home/boundary/boundary.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/boundary.service
    systemctl daemon-reload
    systemctl enable boundary.service
    echo "On reboot the service should start see /home/boundary/boundary.log for details"
    echo "boundary service install complete"   
    pause
  fi
}

#------------------------------------------------------------------------------
function loraService ()
{
  if (whiptail --title "Install the lora service py" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat > /lib/systemd/system/lora.service <<EOF
#filename:  /lib/systemd/system/lora.service
[Unit]
Description=Start lora program

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/rtk;/usr/bin/python3 radio_rfm9x.py > /home/rtk/radio_rfm9x.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/lora.service
    systemctl daemon-reload
    systemctl enable lora.service
    echo "On reboot the service should start see /home/rtk/lora.log for details"
    echo "boundary service install complete"   
    pause
  fi
}

#------------------------------------------------------------------------------
function installMono ()
{
  if (whiptail --title "install mono (this will take a minute or two" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    apt --fix-broken install  
    sudo apt-get -y install mono-runtime mono-complete
    echo "mono installed"
    echo "you may need to nano /etc/apt/sources.list"
    echo "use instead:"
    echo "deb http://reflection.oss.ou.edu/raspbian/raspbian/ buster main contrib non-free rpi"
    echo "then apt-get update"
    pause
  fi
}


#------------------------------------------------------------------------------
function runXspServer ()
{
  if (whiptail --title "start xsp server" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cd /home/xsp;mono bin/xsp4.exe --port 80
    echo "/home/xsp server running on port 80, note: make a server that will run after a few seconds and run this server" 
    pause
  fi
}


#------------------------------------------------------------------------------
function repairTouchscreen ()
{
  if (whiptail --title "repair Touchscreen" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    
    apt-get update
    apt --fix-broken install
    cd /home/LCD-show
    ./LCD35-show 0
    echo "Touchscreen has been repaired" 
    pause
  fi
}


#------------------------------------------------------------------------------
function do_upgrade ()
{
  if (whiptail --title "apt-get dist-upgrade (this will take awhile)" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    apt-get dist-upgrade
    echo "Upgrade complete"   
    pause
  fi
}


#------------------------------------------------------------------------------
function addXspBashrc ()
{
  if (whiptail --title "Add xsp4.exe to /home/pi/.bashrc" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat >> /home/pi/.bashrc <<EOF
cd /home/xsp;sudo mono bin/xsp4.exe --port 80

EOF
    echo "xsp4 will now run on startup"   
    pause
  fi
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
function addCrontab () 
{
    sudo bash -c "crontab -l > /boot/mycron"
    echo "$1" >> /boot/mycron

    echo "Current crontab settings:"
    cat /boot/mycron
    crontab /boot/mycron  c    echo "Added $1 to crontab"   
}

#------------------------------------------------------------------------------
function do_crontab ()
{
  if (whiptail --title "Update crontab" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    #list current crontab
    sudo bash -c "crontab -l > /boot/mycron"

    echo "@reboot cd /home/xsp/data;./cleanJs.py" > /boot/mycron
    echo "#@reboot cd /root/webserver;./webserver.py" >> /boot/mycron
    echo "#@reboot cd /share/WODRaspberryPiPython;python waitInternet.py;git reset --hard origin/master;git pull --all;cp sshBackdoor.py /share/WOD/." >> /boot/mycron

    echo "Current crontab settings:"
    cat /boot/mycron
    crontab /boot/mycron  c    echo "Crontab updated"   
    pause
  fi
}


#------------------------------------------------------------------------------
function do_samba ()
{
  if (whiptail --title "Install /share shared drive using SAMBA" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 

     sudo apt-get -y install samba samba-common-bin
     cd /
     mkdir -m 0777 /share
     mkdir -m 0777 /share/data
     mkdir -m 1777 /share/data/saved
     mkdir -m 1777 /share/data/archived
     sudo cp -r /boot/share /
  
     sudo apt-get -y install samba samba-common-bin
     if grep -Fxq "[share]" /etc/samba/smb.conf
     then
        echo "/etc/samba/smb.conf already has [share] defined"
     else
        echo "adding [share] to /etc/samba/smb.conf" 
        cat >> /etc/samba/smb.conf <<EOF
[share]
Comment = Pi shared folder
Path = /share
Browseable = yes
Writeable = yes
only guest = no
create mask = 0777
directory mask = 0777
Public = yes
Guest ok = yes

EOF

    fi
    # create samber user: root and set password
    (echo "raspberry";echo "raspberry") | smbpasswd -a root
    # restart samba
    /etc/init.d/samba-ad-dc restart
    echo "Samba installed"   
    pause
  fi
}

#------------------------------------------------------------------------------
function makeBoundary ()
{
   echo "Make Boundary"
   echo "You will need 3 directories: "
   echo "pnmea2-master, boundary and xsp (from boundary)"
   pause
   
   # Copy DIRECTORIES
   cd /boot
   cp -R xsp /home/
   cd /home/xsp
   chmod +x *.*
   cd /boot 
   cp -R boundary /home/
   cd /home/boundary
   chmod +x *.*
   cd /boot/pynmea2-master
   python setup.py install
   cd /boot
   installMono 
   addXspBashrc 
   do_boundary_service
   do_ssh 
   do_keyboard 
   do_timezone
   accessPoint
   addBridge   
   pause
}

#------------------------------------------------------------------------------
function configRtk ()
{ 
   echo "You will need webServer, pynmea2-master directory and an rtk directory located in the /boot directory"
   if (whiptail --title "Config rtk ssh, keyboard, timezone, 3.5 display" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
      apt-get update
      # general configuration
      do_ssh 
      do_keyboard 
      do_timezone 

      # install libraries
      #installPynmea2 
      cd /boot
      cd pynmea2-master
      sudo python3 setup.py install 

      # Copy DIRECTORIES
      cd /boot 
      cp -R webServer /home/
      cd /home/webServer
      chmod +x *.*
      
      cd /boot      
      cp -R rtk /home/
      cd /home/rtk
      chmod +x *.*
      cd /boot

      
      # install services 
      readNmeaService
      ipService
      pyWebService
      iaRtnService
      
      echo "The next install will cause a unit reboot"
      # do_35display
      
      echo "Device is now configured for rtk, you will need to log into the webserver and set username/password, and run 3.5 display (command o)"
      pause
   fi
}

#------------------------------------------------------------------------------
function doRtk ()
{ 
   # Copy DIRECTORIES
   cd /boot
   cp -R xsp /home/
   cd /home/xsp
   chmod +x *.*
   cd /boot 
   cp -R rtk /home/
   cd /home/rtk
   chmod +x *.*
   cd /boot/pynmea2-master
   python setup.py install
   cd /boot
   installMono 
   addXspBashrc 
   do_rtk_service
   do_ssh 
   do_keyboard 
   do_timezone 
}

#------------------------------------------------------------------------------
function loraHat ()
{
   if (whiptail --title "Install python libraries for lora hat" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
      # Copy DIRECTORIES
      pip3 install adafruit-circuitpython-ssd1306
      pip3 install adafruit-circuitpython-framebuf
      pip3 install adafruit-circuitpython-rfm9x 
      #pip3 install RPI.GPIO #already installed
      pip3 install adafruit-blinka   
      #Download fonts
      cd /home/rtk
      # wget https://github.com/adafruit/Adafruit_CircuitPython_framebuf/blob/master/examples/font5x8.bin  
      cd /boot  
      loraService     
      do_ssh      
      
      echo "SPI and I2C should be enabled via raspi-config"
   fi
}

#------------------------------------------------------------------------------
function rtkRover ()
{
   echo "Make RTK Rover"
   echo "You will need 3 directories: "
   echo "pnmea2-master, rtk and xsp (from rtk)"
   echo "Wifi of the rover should be configured, country=US and, login to the rtk base (ABCD1234)"
   pause  
   doRtk   
   cd /home/rtk
   touch rover
   dc /boot  
  
   pause
}

#------------------------------------------------------------------------------
function addCountryCode ()
{
  if (whiptail --title "Add country=US to /etc/wpa_supplicant/wpa_supplicant.conf" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then 
     if line_exists_in /etc/wpa_supplicant/wpa_supplicant.conf "country=US"
     then
        echo "country=US already exists in /etc/wpa_supplicant/wpa_supplicant.conf"
     else
        echo "Adding country=US to /etc/wpa_supplicant/wpa_supplicant.conf"
        echo "contry=US">>/etc/wpa_supplicant/wpa_supplicant.conf
     fi
     pause
  fi
}



#------------------------------------------------------------------------------
function pySimpleServerService ()
{
  if (whiptail --title "Run py server service on start?" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat > /lib/systemd/system/simpleServer.service <<EOF
#filename:  /lib/systemd/system/simpleServer.service
[Unit]
Description=Start py server

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/simpleServer;/usr/bin/python3 simpleServer.py > /home/simpleServer/simpleServer.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/simpleServer.service
    systemctl daemon-reload
    systemctl enable simpleServer.service
    echo "Check crontab to make sure sshBackdoor.py is not getting downloaded on reboot."
    echo "On reboot the simple server service should start see /home/simpleServer/simpleServer.log for details" 
    pause
  fi
}

#------------------------------------------------------------------------------
function pyHapServerService ()
{
  if (whiptail --title "Run py server service on start?" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat > /lib/systemd/system/pyServer.service <<EOF
#filename:  /lib/systemd/system/pyServer.service
[Unit]
Description=Start py server

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/simpleServer;/usr/bin/python hapServer.py > /home/simpleServer/hapServer.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/pyServer.service
    systemctl daemon-reload
    systemctl enable pyServer.service
    echo "On reboot the simple server service should start see /home/simpleServer/simpleServer.log for details" 
    pause
  fi
}

#------------------------------------------------------------------------------
function piServer ()
{
   if (whiptail --title "Create a pi server" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then   
      cd /boot  
      # Copy DIRECTORIES
      cp -R rtk /home/
      cd /home/rtk
      chmod +x *.*
      #cd /boot/pynmea2-master
      #python setup.py install
      #cd /boot
      
      
      #Note: No wifi for this server, it is connected by LAN
      # Mono is for the .exe which will get data from the sites.
      installMono 
      do_ssh 
      do_keyboard 
      do_timezone 
      ipService
      pyHapServerService
      # 35 display will cause reboot 
      do_35display
   fi
}

#------------------------------------------------------------------------------
function piWOD ()
{
   if (whiptail --title "Create a WOD pi (note this does not work yet)" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then   
      cd /boot  
      # Copy DIRECTORIES
      cp -R rtk /home/
      cd /home/rtk
      chmod +x *.*
      cd /boot/pynmea2-master
      python setup.py install
      cd /boot
      echo "Copy xsp directory"
      cp -R xsp /home/
      cd /home/xsp
      chmod +x *.*
      cd /boot
      
      echo "You will need the current WOD directory to reside in the /boot directory"
      echo "Copy WOD to /share"
      cp -R WOD /share/
      
      echo "Need to copy sshBackdoor.py over..."
      pause
      
      apt-get install sshpass 
      
      #Note: No wifi for this server, it is connected by LAN
      # Mono is for the .exe which will get data from the sites.
      installMono 
      do_ssh 
      do_keyboard 
      do_timezone 
      #ipService
      #pyHapServerService
      # 35 display will cause reboot 
      do_35display
   fi
}


#------------------------------------------------------------------------------
function rtkBase ()
{
   echo "Make RTK Base Station"
   echo "You will need 3 directories: "
   echo "pnmea2-master, rtk and xsp (from rtk)"
   echo "You may need to set wifi Country, and then set static wlan0?"
   pause   
   doRtk
   accessPoint
   # Modify /etc/hostapd/hostapd.conf to create the ap1 access point
   cat > /etc/hostapd/hostapd.conf <<EOF
interface=wlan0
driver=nl80211
ssid=rtkBase
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
   
   pause
}


#------------------------------------------------------------------------------
function cameraService ()
{
  if (whiptail --title "Install the camera service py" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then  
    cat > /lib/systemd/system/camera.service <<EOF
#filename:  /lib/systemd/system/camera.service
[Unit]
Description=Start cameraprogram

[Service]
Environment=DISPLAY=:0
Environment=XAUTHORITY=/home/pi/.Xauthority
ExecStart=/bin/bash -c 'cd /home/piTank;/usr/bin/python3 streamCamera.py > /home/piTank/streamCamera.log 2>&1'
Restart=always
RestartSec=10s
KillMode=process
TimeoutSec=infinity

[Install]
WantedBy=graphical.target


EOF

    chmod 644 /lib/systemd/system/camera.service
    systemctl daemon-reload
    systemctl enable camera.service
    echo "On reboot the service should start see /home/piTank/streamCamera.log for details"
    echo "camera service install complete"   
    pause
  fi
}

#------------------------------------------------------------------------------
function upgradeWodServer ()
{
  echo "You will need the simpleServer directory located on boot drive"
  if (whiptail --title "Upgrade WOD pi to serve data" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then   
     cd /boot
     
     echo "Copy simpleServer directory to /home (you only need a /home/xsp/data directory)"
     cp -R simpleServer /home/
     cd /home/simpleServer
     chmod +x *.*
     cd /boot
     
     # Create data directory for .js files to land in
     mkdir /home/xsp
     mkdir /home/xsp/data
     
     pySimpleServerService 
 
     echo "Edit /etc/lighttpd/lighttpd.conf change port 80 to port 85"
     pause
     echo "Make sure data is getting copied to /home/xsp/data directory"

     echo "Update sshBackdoor.py" 
     cp /boot/WOD/*.py /share/WOD/.     
     cp /boot/WOD/*.jpg /share/WOD/.
                   
     echo "Updated /share/WOD/*.jpg and /share/WOD/*.py"
     pause
  fi
}

#------------------------------------------------------------------------------
function tankService ()
{
   echo "Set up tank service"
   pause   
   # Copy DIRECTORIES
   cd /boot
   cp -R piTank /home/
   cd /home/piTank
   chmod +x *.*
   cd /boot 
   do_ssh 
   do_keyboard 
   # addCountryCode (not working)
   do_timezone    
   cameraService
   do_static
   echo "Enable the camera using raspi-config"
   pause
}

#------------------------------------------------------------------------------
function detectCamera ()
{
   echo "Check if camera is detected and supported"
   pause  
   vcgencmd get_camera
   
   echo "should show detected=1 and supported=1"
   pause
}

#------------------------------------------------------------------------------
#function piTop ()
#{
#  # echo "You will need the simpleServer directory located on boot drive"
#  #if (whiptail --title "Setup piTop running raspbian" --yesno "" 8 65 --yes-button "Yes" --no-button "Cancel" ) then     
#  #   echo "deb http://apt.pi-top.com/pi-top-os sirius main contrib non-free" | sudo tee /etc/apt/sources.list.d/pi-top.list &> /dev/null
#  #   curl https://apt.pi-top.com/pt-apt.asc | sudo apt-key add
#  #   sudo apt update
#  #   sudo apt install --no-install-recommends -y pt-device-manager pt-sys-oled pt-firmware-updater
#  #fi     
#}

#------------------------------------------------------------------------------
function piPair ()
{
   echo "Make a pi server? ..... not sureMake pi pair sd card"
   echo "You will need directories: "
   echo "pnmea2-master, rtk and xsp (from rtk)"
   echo "You may need to set wifi Country, and then set static wlan0?"
   pause   
   # Copy DIRECTORIES
   cd /boot
   cp -R xsp /home/
   cd /home/xsp
   chmod +x *.*
   cd /boot 
   cp -R rtk /home/
   cd /home/rtk
   chmod +x *.*
   cd /boot
   do_rtkPi_service
   do_ssh 
   do_keyboard 
   # addCountryCode (not working)
   do_timezone 
   
   pause
}
#------------------------------------------------------------------------------
function rtkPi ()
{
   echo "Make an RTK Pi (which will login to a wifi network"
   echo "You will need 3 directories: "
   echo "pnmea2-master, rtk and xsp (from rtk)"
   echo "You may need to set wifi Country, and then set static wlan0?"
   pause   
   # Copy DIRECTORIES
   cd /boot
   cp -R xsp /home/
   cd /home/xsp
   chmod +x *.*
   cd /boot 
   cp -R rtk /home/
   cd /home/rtk
   chmod +x *.*
   cd /boot/pynmea2-master
   python setup.py install
   cd /boot
   installMono 
   addXspBashrc 
   do_rtkPi_service
   do_ssh 
   do_keyboard 
   # addCountryCode (not working)
   do_timezone 
   
   pause
}

function connectHC06()
{

   set prompt "#"
   read -p "Enter mac address of HC06 XX:YY:ZZ:QQ:RR:SS" address  


   if ($address eq "") 
   then
      send_user "You must specify mac address of the bluetooth device"
      send_user "Usage example: ./connect.sh 98:D3:31:20:53:D4"
   else 

      spawn sudo bluetoothctl 
      # -a
      send "power on\r"
      sleep 1
      expect -re "Changing"
      send "remove $address\r"
      sleep 1
      expect -re $prompt
      send "pairable on\r"
      sleep 1
      expect -re "Changing"
      send "scan on\r"
      send_user "\nSleeping 15 seconds\r"
      sleep 15
      send_user "\nDone sleeping\r"
      expect -re $prompt
      send "scan off\r"
      expect "Discovery"
      sleep 2
      send_user "\nPair with device\r"
      send "pair $address\r"
      sleep 2
      expect "agent"
      send "1234\r"
      sleep 2
      expect -re "Pairing"
      sleep 1
      send_user "\rShould be now paired.\r"
      send "quit\r"
      expect eof
   fi

}


#------------------------------------------------------------------------------
function do_main_menu ()
{
  SELECTION=$(whiptail --title "Pi4 WOD Main Menu" --menu "Arrow/Enter Selects or Tab Key" 0 0 0 --cancel-button Quit --ok-button Select \
  "0 UPDATE" "apt-get update" \
  "1 HAP" "HAP install" \
  "2 POSTGRES" "Install PostGres" \
  "3 Connect Wise" "Install ConnectWise" \
  "4 /etc/obdcinst.ini" "Make /etc/obdcinst.ini file" \
  "5 Camera" "Stream camera on reboot" \
  "6 Add Country Code" "To /etc/wpa_supplicant/wpa_supplicant.conf" \
  "7 Config Rtk" "Static, SSH, Keyboard, timezone" \
  "8 Display Ip Address" "On Startup using pygame" \
  "9 Iowa Real Time Network Service" "Get corrections on powerup" \
  "A LORA install" "python install" \
  "B LORA service" "Install LORA python program" \
  "C Tank" "Tank image" \
  "D Check Camera" "Check Camera Detected" \
  "E Create a Server" "pi Server" \
  "F Create a py server" "Enable Service" \
  "G Monitor ip address" "Enable Service" \
  "H Create pi" "Pi Pair" \
  "I Create pi" "Wod Pi" \
  "J Simple Server" "Service" \
  "K Upgrade WOD Server" "To Serve Data Daily" \
  "a STATIC IP ADDRESS" "Set Static IP address to 192.168.0.201" \
  "b SSH" "Enable SSH" \
  "c Add Bridge" "Bridge Internet to wifi clients" \
  "d UPGRADE" "Files From GitHub.com" \
  "e RTK Pi" "Build an RTK Pi" \
  "f Keyboard" "US Keyboard" \
  "g Reboot" "Reboot" \
  "h Wifi Login" "Enter SSID and password" \
  "i TIMEZONE" "Set Chicago time zone" \
  "j SAMBA (Select No)" "Set up /share drive" \
  "k CRONTAB" "Update Crontab" \
  "l USBDRIVE" "Configure USB drive detection" \
  "m PYNMEA" "Configure Python NMEA decoding" \
  "n BACKDOOR" "Install backdoor so AS400 can command raspberry pi" \
  "o 3.5 INCH" "Config for 3.5 inch touch screen display (will cause reboot)" \
  "p PIUPDUE" "Install piupdue" \
  "q AP" "Set Wireless Access Point" \
  "r SHUTDOWN" "Shutdown -h now" \
  "s BOUNDARY SERVICE" "Install the boundary service to run on startup" \
  "t DIST UPGRADE" "apt-get dist-upgrade" \
  "u MONO INSTALL" "Install mono" \
  "v Repair Touchscreen" "apt --fix-broken install, and run touchscreen ./LCDShow" \
  "y XSP POWER-UP" "Run the xsp webserver on startup" \
  "z QUIT" "Exit menubox.sh" 3>&1 1>&2 2>&3)
  
  #\
  #"A" "Make Boundary" 3>&1 1>&2 2>&3)
  # "L Pi Top" "Pi Top Running Raspbian" \

  RET=$?
  if [ $RET -eq 1 ]; then
    exit 0
  elif [ $RET -eq 0 ]; then
    case "$SELECTION" in
      0\ *) do_update ;; 
      1\ *) hapInstall ;; 
      2\ *) installPostgres ;;
      3\ *) installConnectWise ;; 
      4\ *) makeEtcObdcinst ;;
      5\ *) streamCamera ;;
      6\ *) addCountryCode ;;
      7\ *) configRtk ;; 
      8\ *) ipService ;;
      9\ *) iaRtnService ;;
      A\ *) loraHat ;;
      B\ *) loraService ;;   
      C\ *) tankService ;;  
      D\ *) detectCamera ;; 
      E\ *) piServer ;; 
      F\ *) pyHapServerService ;;      
      G\ *) monitorIpService ;;
      H\ *) piPair ;;
      I\ *) piWod ;; 
      J\ *) pySimpleServerService ;;
      K\ *) upgradeWodServer ;;
      a\ *) do_static ;;
      b\ *) do_ssh ;; 
      c\ *) addBridge ;;
      d\ *) do_upgrade ;;      
      e\ *) rtkPi ;; 
      f\ *) do_keyboard ;; 
      g\ *) do_reboot ;;
      h\ *) wifiLogin ;;
      i\ *) do_timezone ;; 
      j\ *) do_samba ;; 
      k\ *) do_crontab ;;
      l\ *) do_usbdrive ;;
      m\ *) installPynmea2 ;; 
      n\ *) do_backdoor ;;
      o\ *) do_35display ;;  
      p\ *) do_piupdue ;; 
      q\ *) accessPoint ;;  
      r\ *) do_shutdown ;;  
      s\ *) do_boundary_service ;; 
      t\ *) do_upgrade ;;    
      u\ *) installMono ;;
      v\ *) repairTouchscreen ;;
      y\ *) addXspBashrc ;;       
      z\ *) clear
            exit 0 ;;
         *) whiptail --msgbox "Programmer error: unrecognized option" 20 60 1 ;;
    esac || whiptail --msgbox "There was an error running selection $SELECTION" 20 60 1
  fi
  
  # A\ *) makeBoundary ;;
  # B\ *) addBridge ;;
  # L\ *) piTop ;;

}

#------------------------------------------------------------------------------
#                                Main Script
#------------------------------------------------------------------------------
if [ $# -eq 0 ] ; then
  while true; do
     do_main_menu
  done
fi