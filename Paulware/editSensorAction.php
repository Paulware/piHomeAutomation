<html>
<head> <Title>Edit Sensor Action</Title>
<Script>
<?php
  include "common.inc";
  include "common.php";
  $MAC = getParam ("MAC");
  $SensorId = findSensor($MAC)['ID'];
  echo ( "    var Sensor=$SensorId;\n");
  echo ( "    var MAC='$MAC';\n");
?>

   function deleteAction (ID) {
       url = 'deleteAction.php?ID=' + ID + '&Sensor=' + Sensor + '&MAC=' + MAC;
       window.location.href=url;
   }
   function modifyAction (ID) {
       url = 'modifyAction.php?ID=' + ID + '&Sensor=' + Sensor;
       window.location.href=url;
   }
     
  function addAction () {   
  
     url = 'modifyAction.php?ID=0&Sensor=' + Sensor;
     //alert ( 'go to : ' + url );
     window.location.href = url;             
  }
</Script>
</head>
<body>
<?php
  echo ( "<center><H1>$MAC</H1></center><BR>");
  $row = findSensor($MAC);
  $IpAddress = $row['IpAddress'];
  if ($row) {   
    $sensorId = $row['ID'];
    $count = 0;
    $sql = "Select * From actions Where Sensor = $sensorId";
    $result = query ( $sql );
    while ($row = getResult ($result))     
    // while ($row = mysql_fetch_assoc ($result)) 
    {		         
      $count = $count + 1;
      $action = $row["Action"];
      $Phone = $row["Phone"];
      $ID = $row["ID"];
      $Username = $row["Username"];
      $AffectedMAC = $row["AffectedMAC"];
      $Message = $row["Message"];
      $Subject = $row["Subject"];
      $Body = $row["Body"];
      $Event = $row ["Event"];
      if ($count == 1) {
         //echo ("<br>$action.<br>\n");
         echo ( "<table border=\"2\">\n");
         echo ( "<tr><th>&nbsp;</th><th>When</th><th>Action</th><th>To</th><th>Info</th><th>Delete</th><th>Modify</th></tr>\n");
      }
      if ($action=='text') {
        echo ("<tr><td>$count</td><td>$Event</td><td>$action</td><td>$Phone</td><td>$Message</td><td><input type=\"button\" value=\"Delete\" onclick=\"deleteAction($ID);\"><td><input type=\"button\" value=\"Modify\" onclick=\"modifyAction($ID);\"></td></tr>");                      
      } else if ($action =='email') {
        echo ("<tr><td>$count</td><td>$Event</td><td>$action</td><td>$Username</td><td>$Subject $Body</td><td><input type=\"button\" value=\"Delete\" onclick=\"deleteAction($ID);\"><td><input type=\"button\" value=\"Modify\" onclick=\"modifyAction($ID);\"></td></tr>");            
      } else if ($action == 'off') {
        echo ("<tr><td>$count</td><td>$Event</td><td>$action</td><td>$AffectedMAC</td><td>&nbsp;</td><td><input type=\"button\" value=\"Delete\" onclick=\"deleteAction($ID);\"><td><input type=\"button\" value=\"Modify\" onclick=\"modifyAction($ID);\"></td></tr>");                   
      } else if ($action == 'on') {
        echo ("<tr><td>$count</td><td>$Event</td><td>$action</td><td>$AffectedMAC</td><td>&nbsp;</td><td><input type=\"button\" value=\"Delete\" onclick=\"deleteAction($ID);\"><td><input type=\"button\" value=\"Modify\" onclick=\"modifyAction($ID);\"></td></tr>");                   
      } else if ($action == 'IM') {
        echo ("<tr><td>$count</td><td>$Event</td><td>$action</td><td>$Phone</td><td>$Body</td><td><input type=\"button\" value=\"Delete\" onclick=\"deleteAction($ID);\"><td><input type=\"button\" value=\"Modify\" onclick=\"modifyAction($ID);\"></td></tr>");                   
      } else {
        echo ("<tr><td>$count</td><td>$Event</td><td>$action</td><td>&nbsp;</td><td>&nbsp;</td><td><input type=\"button\" value=\"Delete\" onclick=\"deleteAction($ID);\"><td><input type=\"button\" value=\"Modify\" onclick=\"modifyAction($ID);\"></td></tr>");                                  
      }
    }
    if ($count > 0) {
       echo ("</Table>\n" );
    } else {
       echo ("<Script>addAction();</Script>\n");
    }
    echo ("<p><H2>Add an action</h2>");
    echo ("<input type=\"button\" value=\"Add\" onclick=\"addAction();\">");    
  } 
  
//echo ( "<br>$count Actions detected<br>");  
?>

</body>
</html>