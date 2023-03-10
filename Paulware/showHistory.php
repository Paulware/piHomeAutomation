<html>   
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Entry', 'Fahrenheit Temperature', 'Percent Humidity'],
<?php 

   include "common.inc";
   include "common.php"; 
   $ID = getParam ("ID");
  
   $result = query ( "Select * From sensorvalues where SensorId=$ID" );
   $count = 0;
   while ($row = mysql_fetch_assoc ($result)) {
      $value = $row['Value'];
      if ($count > 0) {
        echo ",\n";
      }
      $count = $count + 1;         
      $colon = strpos ( $value , ":");
      if ($colon == 0) {
        // This should never happen
        $temp = $value;
        $humidity = '12.5';
      } else {
        $temp = substr ($value,0,$colon);
        $humidity = substr ($value,$colon+1);
      }  
      //echo "          // $value\n";
      echo "          ['$count', $temp,  $humidity]";
   }
?>

        ]);

        var options = {
          title: 'Temperature/Humidity',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
  </head>

<body>
<Script>
  //window.location.href='index.php';
</Script>
   <div id="curve_chart" style="width: 900px; height: 500px"></div>
<br>
<input type="button" value="home" onclick="window.location.href='index.php';">
</body>
</html>