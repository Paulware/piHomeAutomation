<html>
<head>
<title>Home Automation</title>
<meta http-equiv="refresh" content="5">
<Script>

  function showHistory(which) {
    window.location.href = 'showHistory.php?ID=' + which;
  }
  function modifyNickname(MAC,Nickname) {
     window.location.href = 'updateNickname.php?Nickname=' + Nickname + '&MAC=' + MAC;     
  }
  function changeAction(MAC) {
     window.location.href = 'editSensorAction.php?MAC=' + MAC;
  }
  function deleteSensor(MAC) {
     window.location.href = 'deleteSensor.php?MAC=' + MAC;
  }
</Script>
</head>
<body>
<p><center style="font-size:200%">Home Automation Center</center></p>
<hr>

<?php

   include "common.inc";
   include "common.php";
   
   $result = query ( "Select * From lostsensors" );
   $count = 0;

   //while ($row = mysql_fetch_assoc ($result)) {
   while ($row = getResult ($result)) { 
      $MAC = $row['MAC'];
      
      if ($MAC != '') {
         $sql = "Select * From sensors Where MAC='$MAC'";
         $q = query ("$sql");
        
         //if (!mysql_fetch_assoc ($q)) {
         if ($r = getResult ($q)) { 
            if ($count==0) {
              echo ("<H1>Sensors not assigned to a type or action<br></h1>\n");      
              echo ( "<Table border=\"0\" width=\"30%\">\n");
              echo ( "<tr><th>MAC</th></tr>\n");
            }
            echo ( "<tr><td>$MAC</td></tr>\n");
            $count = $count + 1;         
         }   
      }   
   }
   if ($count == 1) {
      echo ( "</Table>\n");
      echo ( "<p>\n");
      echo ("<H1>Sensors assigned to types<br></h1>\n");      
   } else {
      echo ("No lost sensors detected<br>");
   } 
  
      
   $result = query ( "Select * From sensors" );
   $count = 0;
   while ($row = getResult ($result))   {		 
      if ($count == 0) {
         echo ("<table border=\"0px solid\" width=\"90%\">\n" );
         echo ("<tr bgcolor=\"lightgray\"><th width=\"10%\" align=\"center\">Type</th><th width=\"15%\" align=\"center\">MAC</th><th width=\"15%\" align=\"middle\">IP Address</th><th width=\"10%\" align=\"center\">Value</th><th width=\"15%\" align=\"center\">Timestamp</th><th width=\"10%\" align=\"center\">Delete</th><th width=\"10%\">Location</th><th width=\"10%\">Action</th><th>History</th></tr>\n");
      }  
      $Id = $row["ID"];
      $TypeName = $row["TypeName"];
      $MAC = $row["MAC"];
      $Value = $row["Value"];
      $Timestamp = $row["Timestamp"];
      $Location = $row["Nickname"];
      $IpAddress = $row["IpAddress"];
      $Humidity = $row["Humidity"];
      if ($Humidity != '') {
         $Value = "$Humidity:$Value";
      }
      if ($TypeName == "picamera") { 
        print ("<tr><td align=\"center\">$TypeName</td><td align=\"center\">$MAC</td><td align=\"center\">$IpAddress</td><td align=\"center\"><input type=\"button\" value=\"View\" onclick=\"window.location.href='http://$IpAddress:8080/stream';\"</td><td align=\"center\"> $Timestamp</td>");        
      } else {
        if ($TypeName == "cameratank") {
          print ("<tr><td align=\"center\">$TypeName</td><td align=\"center\">$MAC</td><td align=\"center\">$IpAddress</td><td align=\"center\"><input type=\"button\" value=\"View\" onclick=\"window.location.href='viewTank.php?IpAddress=$IpAddress&MAC=$MAC';\"</td><td align=\"center\"> $Timestamp</td>");        
        } else {
          print ("<tr><td align=\"center\">$TypeName</td><td align=\"center\">$MAC</td><td align=\"center\">$IpAddress</td><td align=\"center\">$Value</td><td align=\"center\"> $Timestamp</td>");
        }  
      }

      if ($MAC == "timer") {
         print ("<td>&nbsp;</td><td>&nbsp;</td>");
      } else {
         print ("<td align=\"center\"><input type=\"button\" value=\"Delete\" onclick=\"deleteSensor('$MAC');\"></td><td><input name=\"location$count\" value=\"$Location\" onchange=\"modifyNickname('$MAC',this.value);\"></td>");       
      }   
      print ("<td><input onclick=\"changeAction('$MAC');\" type=\"button\" value=\"Modify Action\"></td><td><input type=\"button\" value=\"Show History\" onclick=\"showHistory($Id);\"></td></tr>");       
      $count = $count + 1;
   }
    
?>

</body>
</html>