<html>
<body>
<?php
include "common.inc";
include "common.php";

function sendText ($phone, $provider, $body) {
  if ($provider == 'alltel') {
    $recipient = "$phone@message.alltel.com";      
  } else if ($provider == 'att') {
    $recipient = "$phone@txt.att.net";            
  } else if ($provider == 'boost') {
    $recipient = "$phone@myboostmobile.com";           
  } else if ($provider == 'cricket') {
    $recipient = "$phone@sms.mycricket.com";           
  } else if ($provider == 'metro') {
    $recipient = "$phone@mymetropcs.com";           
  } else if ($provider == 'nextel') {
    $recipient = "$phone@messaging.nextel.com";           
  } else if ($provider == 'qwest') {
    $recipient = "$phone@qwestmp.com";           
  } else if ($provider == 'tmobile') {
    $recipient = "$phone@tmomail.net";           
  } else if ($provider == 'sprintpcs') {
    $recipient = "$phone@messaging.sprintpcs.com";            
  } else if ($provider == 'sprintpm') {
    $recipient = "$phone@pm.sprint.com";           
  } else if ($provider == 'suncom') {
    $recipient = "$phone@tms.suncom.com";           
  } else if ($provider == 'uscellular') {
    $recipient = "$phone@email.uscc.net";           
  } else if ($provider == 'verizon') {
    $recipient = "$phone@vtext.com";          
  } else if ($provider == 'virgin') {
    $recipient = "$phone@vmobl.com";          
  }
  $subject = '';
  $message = "$recipient`$subject`$body";
  $command = "C:/python27/python sendMail.py \"$recipient\" \"$subject\" \"$body\"";
  echo ( "<h1>CMD new:</h1><br>$command<BR>\n");
  exec($command);	 
  echo ( "Email was sent");
}

$MAC = getParam ("MAC");
$value = getParam ("value");
$Sensor = findSensor($MAC)['ID'];

$sql = "UPDATE Sensors SET value='$value' WHERE MAC='$MAC'";
echo "$sql";

$result = mysql_query($sql) or die("Could not execute: $sql");  

$sql = "UPDATE Sensors SET Timestamp=CURRENT_TIMESTAMP WHERE MAC='$MAC'";
echo "$sql";
$result = mysql_query($sql) or die("Could not execute: $sql");  
echo "Success $MAC=$value<BR>"; 

$sql = "Select * From actions Where Sensor=$Sensor";
echo "$sql<br>\n";
$result = query ($sql);

$count = 0;
while ($row = mysql_fetch_assoc ($result)) {
  $count = $count + 1;
  $Action = $row['Action'];
  $Event = $row['Event'];
  $Message = $row ['Message'];
  $Phone = $row['Phone'];
  $Provider = $row['Provider'];
  $ID = $row['ID'];
  echo "<p>Found an Action: $Action with Event: $Event and Message: $Message, Phone: $Phone, Provider: $Provider, for Sensor: $Sensor<br>\n";
  if (($Action == 'text') && ($Event == 'water')) {
     if ($value == 1) {
		 echo "<br>Action does not qualify because water was not detected yet";
     } else { // Water was detected
         $sql = "UPDATE actions SET LastEvent=CURRENT_TIMESTAMP WHERE ID=$ID";
         echo "$sql";
         $result = query ($sql);
         sendText ($Phone, $Provider, $Message);
	 }
  } else {
	 echo "<br>Action does not qualify<br>\n";
  }
  
}

if ($count == 0) {  
  echo "<p>No Action Found for Sensor: $Sensor<br>\n";
} else {
  echo "<p>Found $count actions for Sensor: $Sensor<br>\n";    
}

       
?>
</body>
</html>