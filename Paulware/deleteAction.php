<html>
<body>
<?php
include "common.inc";
include "common.php"; 
		   
$ID = getParam ("ID");
$MAC = getParam ("MAC");
$sql = "Delete from actions where ID=$ID";
echo ("$sql<br>\n");
$result = mysql_query($sql) or die("Could not execute: $sql");  
echo "<Script>\n";
echo "  var MAC='$MAC';\n";
?>
  window.location.href = 'index.php';
</Script>
</body>
</html>