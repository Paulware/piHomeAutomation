<?php
  include "common.inc";
  include "common.php";
  $username = getParam ( "username");
  $password = getParam ( "password");
?>

<html>
<head> <Title>Update Gmail Configuration</Title>
</head>
<body>
<?php
  $filename = "/var/www/html/Paulware/GmailConfig.py";
  $handle = fopen ($filename, "w") or die ("Cannot open $filename" );
  $d = "class GmailConfig():\n  def __init__ (self):\n   self.login = '$username'\n   self.password = '$password'\n";
  echo ("write: \n$d" );
  fwrite ($handle, $d);
  fclose ($handle);

  echo ( "<h1>CMD:</h1><br>GmailConfig.py created for [$username,$password]<BR>\n"); 
?>
<br>
<input type="button" value="back" onclick="window.location.href='configure.php';">
</Body>
</html>