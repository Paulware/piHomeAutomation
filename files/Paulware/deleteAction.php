<html>
<body>
<?php
include "common.inc";
include "common.php"; 
		   
$ID = getParam ("ID");
$sql = "Delete from actions where ID=$ID";
echo ("$sql<br>\n");
$result = mysql_query($sql) or die("Could not execute: $sql");  

?>
<Script>
  window.location.href='index.php';
</Script>
</body>
</html>