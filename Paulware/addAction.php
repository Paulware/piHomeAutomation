<html>
<body>
<?php
include "common.inc";
include "common.php"; 
		   
$Sensor           = getParam ("Sensor");
$Action           = getParam ("Action");
$Event            = getParam ("Event");
$Message          = getParam ("Message");
$Phone            = getParam ("Phone");
$Provider         = getParam ("Provider");
$ID               = getParam ("ID");
$Username         = getParam ("Username");
$Subject          = getParam ("Subject");
$Body             = getParam ("Body");
$AffectedMAC      = getParam ("AffectedMAC");
$TimeValue        = getParam ("TimeValue");
$HumidityValue    = getParam ("HumidityValue");
$TriggerValue     = getParam ("TriggerValue");
$FrequencySeconds = getParam ("FrequencySeconds");
$IButtonValue     = getParam ("IButtonValue");

if ($ID==0) {
  $sql = "Insert into actions (Sensor,Action,Event,Message,Phone,Provider,Username,Subject,Body,AffectedMAC,TimeValue,HumidityValue,TriggerValue,FrequencySeconds,IButtonValue) values ( $Sensor, '$Action','$Event','$Message','$Phone', '$Provider','$Username', '$Subject', '$Body','$AffectedMAC','$TimeValue','$HumidityValue','$TriggerValue',$FrequencySeconds,'$IButtonValue')";
} else {
  $sql = "Update actions Set FrequencySeconds=$FrequencySeconds, TriggerValue='$TriggerValue', HumidityValue='$HumidityValue', TimeValue='$TimeValue', AffectedMAC='$AffectedMAC', Action='$Action', Sensor=$Sensor, Event='$Event', Message='$Message', Phone='$Phone', Provider='$Provider', Username='$Username', Subject='$Subject', Body='$Body', IButtonValue='$IButtonValue' Where ID=$ID";
}  
echo ("$sql<br>\n");
$result = mysql_query($sql) or die("Could not execute: $sql");  

echo "Success $Action added for Sensor: $Sensor<BR>"; 
?>
<Script>
  //window.location.href='index.php';
</Script>
<br>
<input type="button" value="home" onclick="window.location.href='index.php';">
</body>
</html>