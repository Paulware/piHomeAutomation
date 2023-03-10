<?php
  include "common.inc";
  include "common.php";
  $AffectedMAC   = getParam ("AffectedMAC");
  $Sensor        = getParam ("Sensor");
  $Action        = getParam ("Action");
  $Event         = getParam ("Event");
  $Phone         = getParam ("Phone");
  $Username      = getParam ("Username");
  $Message       = getParam ("Message");
  $Subject       = getParam ("Subject");
  $Body          = getParam ("Body");
  $Provider      = getParam ("Provider");
  $TriggerValue  = getParam ("TriggerValue");
  $HumidityValue = getParam ("HumidityValue");
?>

<html>
<head> <Title>Test Sensor Action</Title>
</head>
<body>
<?php
  $IpAddress = MACtoIp ($AffectedMAC);
  echo ("Reverse Ip lookup for $AffectedMAC $IpAddress<br>\n");
  echo ( "Sensor:$Sensor Action:$Action Event:$Event Phone:$Phone Username:$Username Message:$Message Subject:$Subject Body:$Body TriggerValue:$TriggerValue<BR>");
  handleAction ($Action, $Username, $Subject, $Body, $IpAddress, $Provider, $Message, $Phone); 
?>
<br>
<input type="button" value="back" onclick="window.history.back();">
</Body>
</html>