<html>
<body>
<?php
include "common.inc";
include "common.php"; 
		   
$Sensor = getParam ("Sensor");
$Action = getParam ("Action");
$Event = getParam ("Event");
$Message = getParam ("Message");
$Phone = getParam ("Phone");
$Provider = getParam ("Provider");
$ID = getParam ("ID");
$Username =getParam("Username");
$Subject=getParam("Subject");
$Body=getParam("Body");

if ($ID==0) {
  $sql = "Insert into actions (Sensor,Action,Event,Message,Phone,Provider,Username,Subject,Body) values ( $Sensor, '$Action','$Event','$Message','$Phone', '$Provider','$Username', '$Subject', '$Body')";
} else {
  $sql = "Update actions Set Action='$Action', Sensor=$Sensor, Event='$Event', Message='$Message', Phone='$Phone', Provider='$Provider', Username='$Username', Subject='$Subject', Body='$Body' Where ID=$ID";
}  
echo ("$sql<br>\n");
$result = mysql_query($sql) or die("Could not execute: $sql");  

echo "Success $Action added for Sensor: $Sensor<BR>"; 
?>
<Script>
  window.location.href='index.php';
</Script>
</body>
</html>