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

echo "install required software"
apt-get install dnsmasq hostapd -y

echo "turn off services"
systemctl stop dnsmasq
systemctl stop hostapd

if line_exists_in /etc/dhcpcd.conf "interface wlan0"
then
  echo " "
  echo ":) /etc/dhcpcd.conf already has interface wlan0"
else
  echo "setting static ip_address in /etc/dhchpcd.conf"
  echo "interface wlan0" >> /etc/dhcpcd.conf
  echo "   static ip_address=192.168.4.1/24" >> /etc/dhcpcd.conf
  echo "   nohook wpa_supplicant" >> /etc/dhcpcd.conf
  echo "   static routers=192.168.4.1" >> /etc/dhcpcd.conf

fi
service dhcpcd restart

mv /etc/dnsmasq.conf /etc/dnsmasq.conf.orig
echo "interface=wlan0" > /etc/dnsmasq.conf
echo "  dhcp-range=192.168.4.2,192.168.4.102,255.255.255.0,72h" >> /etc/dnsmasq.conf

cat > /etc/hostapd/hostapd.conf <<EOF
interface=wlan0
driver=nl80211
ssid=pi4
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

sed -i -- 's/#DAEMON_CONF="\/etc\/hostapd\/hostapd.conf"/DAEMON_CONF="\/etc\/hostapd\/hostapd.conf"/g' /etc/default/hostapd
sed -i -- 's/#DAEMON_CONF=""/DAEMON_CONF="\/etc\/hostapd\/hostapd.conf"/g' /etc/default/hostapd

systemctl start hostapd
systemctl start dnsmasq

sed -i -- 's/#net.ipv4.ip_forward=1""/net.ipv4.ip_forward=1""/g' /etc/sysctl.conf

# Why doing masquerage? 
iptables -t nat -A  POSTROUTING -o eth0 -j MASQUERADE
sh -c "iptables-save > /etc/iptables.ipv4.nat"

if line_exists_in /etc/rc.local "iptables-restore"
then

sed -i -- 's/exit 0""/# exit 0""/g' /etc/rc.local
echo "iptables-restore < /etc/iptables.ipv4.nat" >> /etc/rc.local
echo "exit 0" >> /etc/rc.local

fi
# Work-around for a hostapd bug introduced in Mar/2019
echo "Starting hostpad.service.  Unmasking Unit hostapd.service." 
sudo systemctl unmask hostapd
sudo systemctl enable hostapd
sudo systemctl start hostapd

echo "after reboot your ssid (pi4, password:ABCD1234) should appear"