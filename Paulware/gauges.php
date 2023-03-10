<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Gauge Test</title>
<script src="gauge.min.js"></script>
<style>
body{padding:0;margin:0}
</style>
</head>
<body>

<h1>Sensor Gauges</h1>
<?php 
   include "common.inc";
   include "common.php";
   $result = query ( "Select * From sensors" );
   $count = 0;
   while ($row = mysql_fetch_assoc ($result)) {
      $Id = $row["ID"];
      $TypeName = $row["TypeName"];
      $Location = $row["Nickname"];  
      $Value = $row["Value"];      
      if ($TypeName == "dht11") {
        // dht11 and dht22 has Humidity and Temperature
        $count = $count + 1;
        echo ("<canvas id=\"gauge$count\"></canvas>\n" );
        $count = $count + 1;
        echo ("<canvas id=\"gauge$count\"></canvas>\n" );
      } else if ($TypeName == "security") {    
        echo ("<figure>\n" );
        if ($Value == 1) {
          echo ("<img alt=\"$Location\" title=\"$Location\" src=\"images/lock_closed.jpg\" width=\"200px\">\n" );      
        } else { 
          echo ("<img alt=\"$Location\" title=\"$Location\" src=\"images/lock_open.jpg\" width=\"200px\">\n" );      
        }    
        echo ( "<figcaption>$Location</figcaption>\n" );
        echo ( "</figure>\n" );              
        
      } else if ($TypeName == "Temperature")  {
        $count = $count + 1;
        echo ("<canvas id=\"gauge$count\"></canvas>\n" );
      }       
   }
?>

<div id="console"></div>
<script>

<?php      
   $result = query ( "Select * From sensors" );
   $count = 0;
   while ($row = mysql_fetch_assoc ($result))   {		 
      $Id = $row["ID"];
      $TypeName = $row["TypeName"];
      $MAC = $row["MAC"];
      $Value = $row["Value"];
      $Timestamp = $row["Timestamp"];
      $Location = $row["Nickname"];
      $Humidity = $row["Humidity"];
      if ($TypeName == "dht11") {
         $count = $count + 1;    
         showSensor ($count, $Location, $Value, "Temperature" );
         // dht11 and dht22 also has humidity
         $count = $count + 1;
         showSensor ($count, $Location, $Humidity, "Humidity"); 
      } else if ($TypeName == "security") { 
         //$count = $count + 1;    
         //showSensor ($count, $Location, $Value*100, $TypeName);
      } else if ($TypeName == "Temperature") {
         $count = $count + 1;    
         showSensor ($count, $Location, $Value, $TypeName);
      }       
   }
   
?>

</script>

</body>
</html>
