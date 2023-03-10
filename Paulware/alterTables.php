<html>
<body>
Make Tables required for this project<br>
<?php 
  include "common.inc";
  include "common.php";
  echo ("<br>alter table sensors add column Weight INT<br>"); 
  $q = mysql_query ("alter table sensors add column Weight INT"); 
  
  $result = query ( "Select * From sensors" );
  $count = 0;
  echo ("<br>now get result: Select * from sensors<br>");
  while ($row = mysql_fetch_assoc ($result))   {		 
     $Id = $row["ID"];
     $sql = "Update sensors Set Weight=$Id Where ID=$Id";     
     $res = query ($sql);
  }  
  
  echo ("Table sensors modified.");
?>
</body>
</html>