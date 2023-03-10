<html>
<body>
Make Tables required for this project<br>
<?php 
  include "common.inc";
  include "common.php";
  echo ("<br>drop table sensorvalues<br>"); 
  $q = mysql_query ("Drop Table sensorvalues");
  echo ("Create Table sensorvalues<br>\n");
  $sql = "CREATE TABLE sensorvalues (ID INT AUTO_INCREMENT PRIMARY KEY, Value char(255), Timestamp TIMESTAMP, SensorId INT)";
  $result = mysql_query ($sql);
  
  $q = mysql_query ("Drop Table sensors");
  echo ("Create Table sensors<br>\n");
  $sql = "CREATE TABLE sensors (ID INT AUTO_INCREMENT PRIMARY KEY, MAC char(255), Value char(255), Nickname char(255), TypeName char(255), Action INT DEFAULT 0, IpAddress char(255), Humidity char(255), Timestamp TIMESTAMP, Weight INT)";  
  $result = mysql_query ($sql);
  $sql = "Insert into sensors (MAC,Value,TypeName) Values ('timer', '0','timer')";
  $result = mysql_query ($sql);
  $q = mysql_query ("Drop Table actions");
  echo ("Create table actions<br>\n");
  $sql = "CREATE TABLE actions (ID INT AUTO_INCREMENT PRIMARY KEY, LastEvent TIMESTAMP, Sensor INT, Action char(255), Event char(255), AffectedMAC char(255), Phone char(255), Username char(255), Message char(255), Subject char(255), Body blob, Provider char(255), TimeValue char(255), HumidityValue char(255), TriggerValue char (255), FrequencySeconds INT, IButtonValue char (255))";
  $result = query ($sql);
  $q = mysql_query ("Drop Table lostsensors");
  echo ("Create table lostsensors<br>\n");
  $sql = "CREATE TABLE lostsensors (ID INT AUTO_INCREMENT PRIMARY KEY, MAC char(255), Timestamp TIMESTAMP)";  
  $result = query ($sql);
  
  
  echo ("Tables created.");
?>
</body>
</html>