#!/usr/bin/php
<?php
include "common.inc";
include "common.php";
$value = date ('H:i', time());
$s = findSensor ("timer");
if ($s) { 
   $IpAddress = "localhost";
   $sql = "UPDATE sensors SET Value='$value',Timestamp=CURRENT_TIMESTAMP,IpAddress='$IpAddress' WHERE MAC='timer'";
   echo "$sql\n";
   $result = mysql_query($sql) or die("Could not execute: $sql");  
     
   $Sensor = $s['ID'];
   $isTriggered = false;
   // Handle all the timer actions         
   $sql = "Select * From actions Where Sensor=$Sensor";
   echo "$sql\n";
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
     $FrequencySeconds = $row['FrequencySeconds'];
     $LastEvent        = $row['LastEvent'];
     $AffectedMAC      = $row['AffectedMAC'];
     echo "<p>Found an Action: $Action with Event: $Event and Message: $Message, Phone: $Phone, Provider: $Provider, for Sensor: $Sensor\n";
     $temperature = 0;
     $humidity = 0;
     $isTriggered = getIsTriggered ($Event, $temperature, $TriggerValue, $humidity, $value, $value, $TimeValue);
              
     if ($isTriggered) {   
        // Get difference in time between now and LastEvent 
        echo ( "Last action was triggered $LastEvent\n");
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
        echo "Difference:$diff FrequencySeconds:$FrequencySeconds\n";
        if ($diff < intval ($FrequencySeconds)) {
           echo "Action not triggered because time difference ($diff) < Frequency ($FrequencySeconds)\n";
           $isTriggered = false;           
        } else { // action is triggered
           $sql = "UPDATE actions SET LastEvent=CURRENT_TIMESTAMP WHERE ID=$ID";
           echo "$sql";
           $r = query ($sql);
           $target = findSensor ($AffectedMAC);
           $IpAddress = "";
           if ($target) {
              $IpAddress = $target["IpAddress"];
           }
           handleAction ($Action, $Username, $Subject, $Body, $IpAddress, $Provider, $Message, $Phone); 
           echo ("Done triggering action\n");        
        }  
     } else {
       //echo( "The action was not triggered sleep 1 seconds\n");
       //sleep(1);
     }      
   }

   if ($count == 0) {  
     echo "<p>No Action Found for Sensor: $Sensor\n";   
   }   
}     
?>
