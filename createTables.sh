if [[ "$EUID" -ne 0 ]]
	then 
 echo "Must logged in as root or use sudo"
 echo "To log in as root, you can use putty and log in as root@ip.add.res.s"
 echo "Or you can enter the command:"
 echo "  su root"
 echo "  raspberry"
 echo "Assuming that raspberry is the password for the root user"
 echo "Then enter the command again:"
 echo "./filename.sh"
	exit 1 || return 1
fi

echo "createTables.sh"

# Create database and user: root
mysql -uroot -ppi -e "CREATE DATABASE Paulware /*\!40100 DEFAULT CHARACTER SET utf8 */;"
mysql -uroot -ppi -e "CREATE USER 'root'@'%' IDENTIFIED BY 'pi';"
mysql -uroot -ppi -e "GRANT ALL PRIVILEGES ON Paulware.* TO 'root'@'%' WITH GRANT OPTION;"
mysql -uroot -ppi -e "Update mysql.user set plugin='';"
mysql -uroot -ppi -e "SELECT User, Host, plugin FROM mysql.user;"
mysql -uroot -ppi -e "FLUSH PRIVILEGES;"

# Move paulware directory to /var/www/html/Paulware
rm -rf /var/www/html/Paulware
cp -r Paulware /var/www/html/Paulware

# create database tables
php /var/www/html/Paulware/makeTables.php
