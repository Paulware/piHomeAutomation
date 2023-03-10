<?php
  include "common.inc";
  include "common.php";
  $jabberTx = getParam ( "jabberTx");
  $jabberPassword = getParam ( "jabberPassword");
?>

<html>
<head> <Title>Update Jabber (IM) Configuration</Title>
</head>
<body>
<?php
  //$filename = "/root/.xsend";
  //$myfile = fopen($filename, "w") or die("Unable to open file!");
  //$txt = "Username";
  //fwrite($myfile, $txt + '\n');
  //$password = "PWord";
  //fwrite($myfile, $password + '\n');
  //fclose($myfile);
  
  $cmd = "python makeJabberConfig.py \"$jabberTx\" \"$jabberPassword\"";
  echo ( "<h1>CMD:</h1><br>$cmd<BR>\n"); 
  exec ($cmd);
  echo "Done writing to $filename";
?>
<br>
<input type="button" value="back" onclick="window.location.href='configure.php';">
</Body>
</html>