<?php
  $user = $_COOKIE["user"]; 
?>

<html>
<head>
<Title>Add Sensor</Title>
<body>
<p><center style="font-size:200%">Home Automation Center</center></p>
<hr><center> 

<script>
  <?php
     print ( '  var user=\'$user\';');
  ?>
  function addSensor (value, sensorType) {
    window.location.href = 'addSensorMac.php?mac=' + value + '&sensorType=' + sensorType;
  }

</script>

</head>

<body>
<table><tr><td aligh="left">
<br><b>Add Sensor:</b>
</td></tr>
<tr><td>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sensor's MAC Address:<input name="macAddress"><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sensor Node Type: <Select name="sensorType">
<option value="dht11">dht11 or dht22(Temperature And Humidity)</option>
<option value="temperature">Temperature</option>
<option value="motionSensor">Motion Sensor</option>
<option value="waterSensor">Water Sensor</option>
<option value="relay">Relay</option>
<option value="security">Security</option>
<option value="lcddisplay">LCD Display</option>
<option value="sonoff">Sonoff</option>
<option value="iButtonReader">iButton Reader</option>
<option value="picamera">Raspberry pi Camera</option>
<option value="cameratank">Camera equipped tank</option>
</Select>
</td></tr></table>
<br>
<input type="button" value="Cancel" onclick="window.location.href='index.php';">
<input type=button value="Add" onclick="javascript:addSensor(document.all.macAddress.value, document.all.sensorType.value);">
<hr>
<center><input type="button" value="Trouble Shoot" onclick="window.location.href='troubleShoot.php';">
<hr>
<b>Contact/help:</b> <u><i>paulware@hotmail.com</i></u>
</body>
</html>