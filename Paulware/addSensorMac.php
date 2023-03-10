<?php
  include "common.inc";
  include "common.php";
  
  $mac = getParam ("mac");
  $sensorType=getParam("sensorType");
  $sensorRow = findSensor($mac);
?>

<html>
<head> <Title>Add Sensor</Title>

</head>

<body>

<?php
  echo ("mac address: $mac<br>\n");
  echo ("sensor type: $sensorType<br>\n");
  if ($sensorRow) {
     echo ( "That sensor already exists!");
  } else {
     $sql = "Insert into sensors (MAC, Value,TypeName) values ( '$mac','0','$sensorType')";
     echo ("$sql<br>\n");
     query ($sql);
     echo ( "That sensor does not yet exist, added it to the Sensors Table"); 
     addWeight ( $mac );     
  }
  echo ("<Script>\n");
  echo ("  window.location.href = 'index.php';\n");
  echo ( "</Script>\n");     
?>
</body>
</html>