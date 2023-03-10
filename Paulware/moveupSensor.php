<html>
<head> <Title>Move up sensor</Title>
<?php
  include "common.inc";
  include "common.php";
  $Weight1 = getParam ("Weight1");
  $Weight2 = getParam ("Weight2");
?>
</head>
<body>
<?php
    echo ("Weight1: $Weight1, Weight2: $Weight2<br>");
    
    $sql = "Select * From sensors Where Weight = '$Weight1'";
    $result = query ( $sql );
    
    if ($row = mysql_fetch_assoc ($result)) {
       $Id1 = $row["ID"]; 
       $sql = "Update sensors Set Weight=$Weight1 Where Weight=$Weight2";
       $res = query ($sql);
          
       $sql = "Update sensors Set Weight=$Weight2 Where ID=$Id1";
       $res = query ($sql);
       echo ("Weights swapped<br>" );
    } else {
       echo ("Could not find: MAC:$MAC in the database<br>");
    } 
    
?>
<script>
   window.location.href='index.php';
</script>
</body>
</html>