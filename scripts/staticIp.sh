#!/bin/sh
if [ "$#" -ne 1 ]; then
  echo "You need to specify ip address as the sole parameter to this script"
  echo "Usage:"
  echo "sudo ./staticIp.sh ip.addr.es.s"
  return 1
fi

IPADDRESS=$1
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
  echo "/etc/dhcpcd.conf updated with static ip"
fi

echo "source-directory /etc/network/interfaces.d" > /etc/network/interfaces
