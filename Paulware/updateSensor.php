<html>
<body>
<?php
include "common.inc";
include "common.php";

//$cli = php_sapi_name() 
//echo ("php_sapi_name(): $cli<br>\n" );

$ServerAddress = $_SERVER['SERVER_ADDR'];
$argc = $_SERVER['argc']; 

$MAC = getParam ("MAC");
$value = getParam ("value");
$s = findSensor ($MAC);
if ($s) {
   $IpAddress = $_SERVER['REMOTE_ADDR'];
   $sql = "UPDATE sensors SET Timestamp=CURRENT_TIMESTAMP,IpAddress='$IpAddress' WHERE MAC='$MAC'";
   echo "$sql";
   $result = mysql_query($sql) or die("Could not execute: $sql");  
   
   // Get currentValue
   $sql = "Select * from sensors where MAC='$MAC'";
   $result = mysql_query($sql) or die ("Could not execute: $sql");
   $currentValue = 'unknown';
   if ($row = mysql_fetch_assoc ($result)) {   
     $currentValue = $row['Value'];
     echo ( "currentValue: $currentValue<br>");
   }  
      
   $pos = strpos($value, ':');
   if ($pos) {
     $humidity = substr ( $value , 0 , $pos ); 
     $temperature = substr ( $value, $pos+1);
     $sql = "UPDATE sensors SET Value='$temperature', humidity='$humidity' WHERE MAC='$MAC'";
   } else { 
     $humidity = '';
     $temperature = $value;   
     $sql = "UPDATE sensors SET Value='$value' WHERE MAC='$MAC'";
   }  
   echo "$sql<br>\n";  
   $result = mysql_query($sql) or die("Could not execute: $sql");    
  
   $Sensor = $s['ID'];
   $isTriggered = false;
   $TypeName = $s['TypeName'];
   echo ( "TypeName: $TypeName, IpAddress: $IpAddress" );
   if ($TypeName == 'lcddisplay') { 
     $Action = $s['Action']; // State variable
     if ($value == 'refresh') {
       $Message = displaySensor($ServerAddress, $Action-1);
     } else if ($value == 'next') { 
       if (is_null($Action)) {
         $Message = "^Server Location     $ServerAddress";
         $Action = 1;
       } else { // Send value of $Action sensor
         $Message = displaySensor ($ServerAddress, $Action);
         if ($Message == "") { // Rollover the $Action
            $Message = "^End of Sensor List  ";        
            $Action = 0;
         } else { // Go to the next item
            $Action = $Action + 1;            
         }           
       }  
       $sql = "UPDATE sensors SET Action='$Action' WHERE MAC='$MAC'";
       echo ("<br>$sql<br>\n" );
       $result = mysql_query($sql) or die("Could not execute: $sql");           
     }
     if ($Message == "") {
        $Message = "No Messsage";
     }
     $cmd = "python sendMessage.py $IpAddress \"$Message\"";
     echo ( "<h1>CMD:</h1><br>$cmd<BR>\n");
   	 exec($cmd);	              
   } else {
         
      
     //if ($MAC != 'timer') {
     //   $sql = "insert into sensorvalues (SensorId,value) Values ($Sensor,'$value')";
     //   echo "$sql<br>\n";
     //   $result = query ($sql);    
    // }
        
        
     echo "Success $MAC=$value<BR>"; 

     $sql = "Select * From actions Where Sensor=$Sensor";
     echo "$sql<br>\n";
     $result = query ($sql);

     $count = 0;
     while ($row = mysql_fetch_assoc ($result)) {
       $count            = $count + 1;
       $Action           = $row['Action'];
       $Event            = $row['Event'];
       $Message          = $row['Message'];
       $Phone            = $row['Phone'];
       $Provider         = $row['Provider'];
       $Username         = $row['Username'];
       $Subject          = $row['Subject'];
       $Body             = $row['Body'];
       $ID               = $row['ID'];
       $TriggerValue     = $row['TriggerValue'];
       $TimeValue        = $row['TimeValue'];
       $IButtonValue     = $row['IButtonValue'];
       $FrequencySeconds = $row['FrequencySeconds'];
       $LastEvent        = $row['LastEvent'];
       $AffectedMAC      = $row['AffectedMAC'];
       $AffectedAddress  = MACtoIp($AffectedMAC);
       echo "<p>Found an Action: $Action with Event: $Event and Message: $Message, Phone: $Phone, Provider: $Provider, IButtonValue: $IButtonValue for Sensor: $Sensor<br>\n";

       $isTriggered = getIsTriggered ($Event, $temperature, $TriggerValue, $humidity, $value, $currentValue, $TimeValue, $IButtonValue );
                
       if ($isTriggered) {        
          // Get difference in time between now and LastEvent 
          echo ( "<br>Last action was triggered $LastEvent<br>\n");
          $lastTime = "";
          $delimeter = strpos($LastEvent, '-');
          if ($delimeter) {
            $year = substr ( $LastEvent , 0 , 4 ); 
            $nextDelimeter = strpos ( $LastEvent, '-', $delimeter+1);
            if ($nextDelimeter) {
               $month = substr ( $LastEvent, $delimeter+1, 2);
               $delimeter = $nextDelimeter;
               $nextDelimeter = strpos ($LastEvent,' ', $nextDelimeter + 1);
               if ($nextDelimeter) {
                 $day = substr ( $LastEvent, $delimeter+1, 2);
                 $delimeter = $nextDelimeter;
                 $nextDelimeter = strpos ( $LastEvent, ':', $nextDelimeter + 1);
                 if ($nextDelimeter) {
                   $hour = substr ($LastEvent, $delimeter+1, 2);
                   $delimeter = $nextDelimeter;
                   $nextDelimeter = strpos ( $LastEvent, ':', $nextDelimeter +1);
                   if ($nextDelimeter) {
                     $minute = substr ($LastEvent, $delimeter+1, 2);
                     $second = substr ($LastEvent, $nextDelimeter+1, 2);
                     $lastTime = "$year-$month-$day $hour:$minute:$second";
                   }
                 }
               }
            }
          }  
          if ($lastTime != "") {
             $date1 = strtotime ($lastTime);
          }
          //var_dump ($date1);
          $date2 = time();
          //var_dump ($date2);
          $diff = $date2 - $date1;
          //var_dump ($diff);
          echo "<br><br>Difference:$diff FrequencySeconds:$FrequencySeconds<br>\n";
          
          if (($diff < intval ($FrequencySeconds)) && ($FrequencySeconds != 0))  {
             echo "<br> Action not triggered because time difference ($diff) < Frequency ($FrequencySeconds)<br>\n";
             $isTriggered = false;           
          }
          
          if ($isTriggered) { 
             $sql = "UPDATE actions SET LastEvent=CURRENT_TIMESTAMP WHERE ID=$ID";
             echo "$sql";
             $r = query ($sql);
             handleAction ($Action, $Username, $Subject, $Body, $AffectedAddress, $Provider, $Message, $Phone); 
             echo ("<br>Done triggering action<br>\n");        
          }  
       } else {
         echo( "The action was not triggered sleep 1 seconds<br>\n");
         sleep(1);
       }      
     }

     if ($count == 0) {  
       echo "<p>No Action Found for Sensor: $Sensor<br>\n";   
     }
   }  
} else {
   echo ("<p>Inserting MAC: $MAC into lostsensors table<br>\n");
   $result = query ( "Select * From lostsensors Where MAC='$MAC'" );
   $result = mysql_fetch_assoc($result);   
   if ($result) {
      echo ("$MAC already in lostsensors<br>\n");
   } else {
      $sql = "insert into lostsensors (MAC) Values ('$MAC')";
      echo "$sql<br>\n";
      $result = query ($sql);   
      echo ("Successfully completed $sql <br>\n" );      
   }
}   

?>
</body>
</html>