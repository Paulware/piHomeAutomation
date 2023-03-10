<?php
  $connection = new mysqli ('localhost', 'root', 'raspberry', 'Paulware') or die ('Connection error!');
  echo "Got Connection";
  // if (!mysql_select_db('paulware', $connection)) die ('Could not open paulware');
?>
