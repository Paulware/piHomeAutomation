<html>
<body>
Make a snapshot of the current actions, and sensors table<br>
<?php 
  include "common.inc";
  include "common.php";
  
  $filename = "restoreSnapshot.php";
  $file = fopen( $filename, "w" ); 
  fwrite( $file, "<html>\n<body>\nRestore the snapshot\n<?php\n" );
  fwrite( $file, "  include \"common.inc\";\n" );
  fwrite( $file, "  include \"common.php\";\n" ); 
  
  fwrite ( $file, "  \$q = mysql_query (\"Drop Table sensors\");\n" );
  fwrite ( $file, "  echo (\"Create Table sensors<br>\\n\");\n");
  fwrite ( $file, "  \$sql = \"CREATE TABLE sensors (ID INT AUTO_INCREMENT PRIMARY KEY, MAC char(255), Value char(255), Nickname char(255), TypeName char(255), Action INT DEFAULT 0, IpAddress char(255), Humidity char(255), Timestamp TIMESTAMP, Weight INT)\";\n");  
  fwrite ( $file, "  \$result = mysql_query (\$sql);\n" );
 
  $sql = "Select * From sensors";
  $result = query ($sql);
  $count = 0;
  while ($row = mysql_fetch_assoc ($result)) {
     $MAC = $row["MAC"];
     $Nickname = $row["Nickname"];
     $TypeName = $row["TypeName"];
     $Action = $row["Action"];
     $IpAddress = $row["IpAddress"];
     $Weight = $row["Weight"];     
     $Value = $row["Value"];
     echo ("Got a [TypeName,Value,Weight,Nickname]: [$TypeName,$Value,$Weight,$Nickname]<br>\n" );
     $sql = "Insert into sensors (MAC,IpAddress,TypeName,Nickname"; 
     if ("$Weight" != "") { 
        $sql = "$sql,Weight";
     } 
     $sql = "$sql) Values ('$MAC', '$IpAddress','$TypeName','$Nickname'";
     if ("$Weight" != "") { 
        $sql = "$sql,$Weight";
     } 
     $sql = "$sql)";
     fwrite ( $file, "  \$sql = \"$sql\";\n" );
     fwrite ( $file, "  \$result = mysql_query (\$sql);\n" );       
     $count = $count + 1;
  } 
  fwrite ( $file, "  checkWeights();\n" );
  echo ("$count sensors saved<br>\n" );
  
  fwrite ( $file, "  \$q = mysql_query (\"Drop Table actions\");\n");
  fwrite ( $file, "  echo (\"Create table actions<br>\\n\");\n");
  fwrite ( $file, "  \$sql = \"CREATE TABLE actions (ID INT AUTO_INCREMENT PRIMARY KEY, LastEvent TIMESTAMP, Sensor INT, Action char(255), Event char(255), AffectedMAC char(255), Phone char(255), Username char(255), Message char(255), Subject char(255), Body blob, Provider char(255), TimeValue char(255), HumidityValue char(255), TriggerValue char (255), FrequencySeconds INT, IButtonValue char (255))\";\n" );
  fwrite ( $file, "  \$result = query (\$sql);\n" );

  
  echo ("<br>Select * from actions<br>");   
  $sql = "Select * From actions";
  $result = query ($sql);
  $count = 0;
  while ($row = mysql_fetch_assoc ($result)) {
     // Find the MAC address associated with the sensor      
     $sensor = findSensorId ($row["Sensor"]);
     $MAC = $sensor["MAC"];
     
     $Action = $row["Action"]; // char (255)
     $Event = $row["Event"]; // char (255)
     $AffectedMAC = $row["AffectedMAC"]; // char (255)
     $Phone = $row["Phone"]; // char (255)
     $Username = $row["Username"]; // char(255)
     $Message = $row["Message"]; // char(255)
     $Subject = $row["Subject"]; // char (255)
     $Body = $row["Body"]; // blob (char)
     $Provider = $row["Provider"]; // char (255)
     $TimeValue = $row["TimeValue"]; // char(255)
     $HumidityValue = $row["HumidityValue"]; // char(255)
     $TriggerValue = $row["TriggerValue"]; // char (255)
     $FrequencySeconds = $row["FrequencySeconds"]; // INT
     $IButtonValue = $row["IButtonValue"]; // char (255)
     echo ("Got a MAC $MAC<br>\n");  
     $Action = $row["Action"];
     
     fwrite ( $file, "  \$row = findSensor('$MAC');\n");
     fwrite ( $file, "  \$SensorId = \$row[\"ID\"];\n" );
     fwrite ( $file, "  \$sql = \"Insert into actions (" . 
                     "Sensor,Action,Event,AffectedMAC,Phone,Username,Message,Subject,Body,Provider," . 
                     "TimeValue,HumidityValue,TriggerValue,FrequencySeconds,IButtonValue) Values " .
                     "(\$SensorId,'$Action','$Event','$AffectedMAC','$Phone'," .
                     "'$Username','$Message','$Subject','$Body','$Provider','$TimeValue'," .
                     "'$HumidityValue','$TriggerValue',$FrequencySeconds,'$IButtonValue')\";\n" );
     fwrite ( $file, "  \$result = mysql_query (\$sql);\n" );            
     $count = $count + 1;
  }
  echo ("Handled $count actions<br>\n" );
  
  fwrite ($file, "?>\n" );
  fwrite ($file, "</body>\n" );
  fwrite ($file, "</html>\n" );
  fclose( $file );    
  echo ("restoreSnapshot.php created.<br>\n");
?>
</body>
</html>