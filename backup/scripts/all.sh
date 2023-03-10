echo "Run all scripts to configure the pi"
echo "Note: .img used for this should already have a wifi access point setup"
if [ $(id -u) -ne 0 ]; then echo "You must use sudo: sudo ./all.sh"; exit 1; fi
apt-get update
./ssh.sh
./keyboard.sh
# ./crontab.sh
./timezone.sh
./ap.sh
./ama0.sh
#./mono.sh
#./samba.sh
#./ssd.sh
#./gparted.sh
#./usbdrive.sh
# include bluetooth and obd
#./obd.sh
# backdoor.sh, creates the WOD directory and copies all /boot/*.py files 
#./backdoor.sh
#./nodeServer.sh
# for mounting usbdrv
./notes.sh
#./ap.sh
# The next command will do a reboot
# ./3InChinaSolidDisplay.sh

