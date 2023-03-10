<?php
  include "common.inc";
  include "common.php";
  $user = getParam ("user");
  $password = getParam("password");
?>

<html>
<head> <Title>Checking Login</Title>
</head>
<body>
<?php
  echo ("User: $user, Password:$password<br>");
  $row = findUser($user);
  
  if ($row) {
    $pword = $row['Password'];
    if ($pword == $password) {
       echo ("$user now logged in, set the cookie");
       setcookie ("user", "$user", time()+36000);
       echo ("<Script>window.location.href = 'index.php';</Script>\n");       
    } else {
       echo ("<h1>Bad password please go back and try again.</h1><hr>");
    }    
  } else { 
    echo ( "<h1>Sorry that user does not exist</h1><br>\n");  
  }
  
?>
<input type=button value="back" onclick="window.location.href='index.php';">
</body>

</html>