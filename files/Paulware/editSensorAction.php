<html>
<head> <Title>Edit Sensor Action 1.0</Title>
<Script>
<?php
  include "common.inc";
  include "common.php";
  $MAC = getParam ("MAC");
  $SensorId = findSensor($MAC)['ID'];
  echo ( "    var Sensor=$SensorId;\n");
?>

   function deleteAction (ID) {
       url = 'deleteAction.php?ID=' + ID + '&Sensor=' + Sensor;
       window.location.href=url;
   }
   function modifyAction (ID) {
       url = 'modifyAction.php?ID=' + ID + '&Sensor=' + Sensor;
       window.location.href=url;
   }
     
  function addAction () {   
     url = 'modifyAction.php?ID=0&Sensor=' + Sensor;
     window.location.href = url;             
  }
</Script>
</head>
<body>
<?php
  echo ( "<center><H1>$MAC 1.0</H1></center><BR>");
  $row = findSensor($MAC);
  if ($row) {   
    $sensorId = $row['ID'];
    $count = 0;
    $result = query ( "Select * From Actions Where Sensor = $sensorId" );
    while ($row = mysql_fetch_assoc ($result)) 
    {		         
      $count = $count + 1;
      $action = $row["Action"];
      $Phone = $row["Phone"];
      $ID = $row["ID"];
      $Username = $row["Username"];
      if ($count == 1) {
         echo ( "<table border=\"2\">\n");
         echo ( "<tr><th>&nbsp;</th><th>Action</th><th>To</th><th>Delete</th><th>Modify</th></tr>\n");
      }
      if ($action=='text') {
        echo ("<tr><td>$count</td><td>$action</td><td>$Phone</td><td><input type=\"button\" value=\"Delete\" onclick=\"deleteAction($ID);\"><td><input type=\"button\" value=\"Modify\" onclick=\"modifyAction($ID);\"></td></tr>");                      
      } else {
        echo ("<tr><td>$count</td><td>$action</td><td>$Username</td><td><input type=\"button\" value=\"Delete\" onclick=\"deleteAction($ID);\"><td><input type=\"button\" value=\"Modify\" onclick=\"modifyAction($ID);\"></td></tr>");            
      }
    }
    if ($count > 0) {
       echo ("</Table>\n" );
    }
    echo ("<p><H2>Add an action</h2>");
    echo ("<input type=\"button\" value=\"Add\" onclick=\"addAction();\">");    
  } 
?>

</body>
</html>