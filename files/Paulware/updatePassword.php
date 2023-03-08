<html>
<head>
<title>Update Password</title>
</head>
<body>
<?php
include "common.inc";
include "common.php";

$user = '';
if(isset($_COOKIE["user"])) {  
  $user = $_COOKIE["user"]; 
  $Password = getParam ("Password");
  $sql = "Update users Set Password='$Password' Where Username='$user'";
  echo ("$sql\n<br>");
  $result = query ( $sql);
  echo ( "Password updated\n<br>" );
} else {
  echo ("User not specified");
}
?>
<input type="button" onclick="window.location.href='index.php';" value="Ok">
</body>
</html>