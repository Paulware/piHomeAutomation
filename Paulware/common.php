<?php
   
   $providers = array (
      "3river"         => array ("3 River Wireless", "sms.3rivers.net"),     
      "acs"            => array ("ACS Wireless", "paging.acswireless.com") ,
      "alltel"         => array ("Alltel","message.alltel.com"),
      "att"            => array ("AT&T","txt.att.net"),     
      "bellcanada"     => array ("Bell Canada","txt.bellmobility.ca"),     
      "bellmobilityca" => array ("Bell Mobility (Canada)", "txt.bell.ca"),
      "bluesky"        => array ("Blue Sky Frog", "blueskyfrog.com"),
      "boost"          => array ("Boost Mobile","myboostmobile.com"),
      "cricket"        => array ("Cricket","sms.mycricket.com"),
      "metro"          => array ("Metro PCS","mymetropcs.com"),
      "nextel"         => array ("Nextel","messaging.nextel.com"),
      "qwest"          => array ("Qwest","qwestmp.com"), 
      "tmobile"        => array ("T-Mobile","tmomail.net"),
      "sprintpcs"      => array ("Sprint PCS","messaging.sprintpcs.com"),
      "sprintpm"       => array ("Sprint PM","pm.sprint.com"),
      "suncom"         => array ("Suncom", "tms.suncom.com"),
      "telus"          => array ("Telus (Canada)","msg.telus.com"),
      "uscellular"     => array ("US Cellular","email.uscc.net"),
      "verizon"        => array ("Verizon","vtext.com"), 
      "virgin"         => array ("Virgin Mobile","vmobl.com"),
      "vodafone"       => array ("Vodafone (UK)","vodafone.net"),
   );  
  
  
   function findWorkingDirectory () {
     $scriptFilename = $_SERVER['SCRIPT_FILENAME'];
     $workingDir = "/var/www/Paulware";     
     $pos = strpos($scriptFilename, "html");
     if ($pos !== false) { // html was found in the script filename 
        $workingDir = "/var/www/html/Paulware";
     }
     return $workingDir;
   }
   
  
   function executeCmd ( $cmd ) {
      echo ( "<h1>$cmd</h1>\n");    
      $output = array();
      exec($cmd, $output);
      var_dump( $output);    
   }
  
   function turnDevice ($address, $which, $offOn) {
      $workingDirectory = findWorkingDirectory();
      $cmd = "python $workingDirectory/turnDevice.py \"$address\" \"$which\" \"$offOn\"";
      echo ( "<h1>turnDevice CMD:</h1><br>$cmd<BR>\n");
      exec($cmd);	 
      echo ( "Done turning the device $offOn");
   }  

   function sendEmail ($recipient, $subject, $body) {
      $message = "$recipient`$subject`$body";
      $workingDirectory = findWorkingDirectory();      
      $command = "python $workingDirectory/sendMail.py \"$recipient\" \"$subject\" \"$body\"";
      echo ( "<h1>CMD new:</h1><br>$command<BR>\n");
      exec($command);	 
      echo ( "Done sending email");
   }

   function sendText ($phone, $provider, $body) {
     $email = textEmailAddress ($provider);    
     $recipient = "$phone@$email";
     $subject = '';
     $message = "$recipient`$subject`$body";
     //$t = date ('H:i', time()); 
     $body = $body; //  + ' ' + $t;
     $workingDirectory = findWorkingDirectory();
     $command = "python $workingDirectory/sendMail.py \"$recipient\" \"$subject\" \"$body\"";
     echo ( "<h1>CMD new:</h1><br>$command<BR>\n");
     exec($command);	 
     echo ( "Email was sent");
   }

   function sendIM ($recipient, $message) {
     //$t = date ('H:i', time()); 
     $message = $message; // + ' ' + $t;
     $workingDirectory = findWorkingDirectory();     
     $cmd = "python $workingDirectory/broadcastCommand.py \"python $workingDirectory/xmpppy/send.py $recipient $message\"";
     echo ( "<h1>CMD:</h1><br>$cmd<BR>\n");
     exec($cmd);
     echo ( "Done sending IM");
   }

  
   // Show the value of the sensor
   function showSensor ($gaugeNum, $name, $gaugeValue, $typeName) {
     if (($typeName == "Humidity") || ($typeName == "Temperature")) {     
        echo ( "var gauge$gaugeNum = new Gauge({\n" );
        echo ( "  renderTo  : 'gauge$gaugeNum',\n" );
        echo ( "  width     : 300,\n" );
        echo ( "  height    : 300,\n" );
        echo ( "  glow      : true,\n" );
        echo ( "  units     : '$typeName',\n" );
        echo ( "  title     : '$name',\n" );
        echo ( "  strokeTicks : false,\n" );
        echo ( "  highlights : [\n" );
        if ($typeName == "Humidity") { 
          echo ( "  {\n" );
          echo ( "   from  : 0,\n" );
          echo ( "   to    : 50,\n" );
          echo ( "   color : 'Blue'\n" );
          echo ( "  }, \n" );
          echo ( "  {\n" );
          echo ( "   from  : 50,\n" );
          echo ( "   to    : 100,\n" );
          echo ( "   color : 'Red'\n" );
          echo ( "  }, \n" );
        } else { // $typeName == "Temperature"
          echo ( "  {\n" );
          echo ( "   from  : 0,\n" );
          echo ( "   to    : 32,\n" );
          echo ( "   color : 'Blue'\n" );
          echo ( "  }, \n" );
          echo ( "  {\n" );
          echo ( "   from  : 32,\n" );
          echo ( "   to    : 65,\n" );
          echo ( "   color : 'Khaki'\n" );
          echo ( "  }, \n" );
          echo ( "  {\n" );
          echo ( "   from  : 65,\n" );
          echo ( "   to    : 76,\n" );
          echo ( "   color : 'PaleGreen'\n" );
          echo ( "  }, \n" );
          echo ( "  {\n" );
          echo ( "   from  : 76,\n" );
          echo ( "   to    : 100,\n" );
          echo ( "   color : 'LightSalmon'\n" );
          echo ( "  }\n" );
        }
        echo ( "  ],\n" );
        echo ( "  animation : {\n" );
        echo ( "   delay : 10,\n" );
        echo ( "   duration: 300,\n" );
        echo ( "   fn : 'bounce'\n" );
        echo ( "  }\n" );
        echo ( "  });\n" );
        echo ( "  gauge$gaugeNum.setValue($gaugeValue);\n" );
        echo ( "gauge$gaugeNum.draw();\n" );
     } else if ($typeName == "security") {    
        echo ( "var gauge$gaugeNum = new Gauge({\n" );
        echo ( "  renderTo  : 'gauge$gaugeNum',\n" );
        echo ( "  width     : 300,\n" );
        echo ( "  height    : 300,\n" );
        echo ( "  glow      : true,\n" );
        echo ( "  units     : '$typeName',\n" );
        echo ( "  title     : '$name',\n" );
        echo ( "  strokeTicks : false,\n" );
        echo ( "  highlights : [\n" );
        if ($gaugeValue == "0") { 
          echo ( "  {\n" );
          echo ( "   from  : 0,\n" );
          echo ( "   to    : 100,\n" );
          echo ( "   color : 'Red'\n" );
          echo ( "  } \n" );
        } else {
          echo ( "  {\n" );
          echo ( "   from  : 0,\n" );
          echo ( "   to    : 100,\n" );
          echo ( "   color : 'Green'\n" );
          echo ( "  } \n" );
        }
        echo ( "  ],\n" );
        echo ( "  animation : {\n" );
        echo ( "   delay : 10,\n" );
        echo ( "   duration: 300,\n" );
        echo ( "   fn : 'bounce'\n" );
        echo ( "  }\n" );
        echo ( "  });\n" );
        echo ( "  gauge$gaugeNum.setValue($gaugeValue);\n" );
        echo ( "gauge$gaugeNum.draw();\n" );     
     } else {
        echo ( "\n // Display: $typeName " );
     } 
   }
    
   
   function handleAction ($Action, $Username, $Subject, $Body, $IpAddress, $Provider, $Message, $Phone) {  
     $workingDirectory = findWorkingDirectory();   
     $which = 'main';
     if (strlen ($Action) > 7) { 
       if (substr($Action,0,4) == 'gpio') { 
         $which = substr($Action,4,2);
         $Action = substr ( $Action,6,2);
       }
     }
          
     if (($Action == 'LO') || ($Action == 'HI')) { 
       echo ( "workingDirectory: $workingDirectory" );
       $cmd = "python $workingDirectory/turnDevice.py \"$IpAddress\" \"$which\" \"$Action\"";        
       echo ( "<hr><h2>CMD:</h2>$cmd<hr><BR>\n");
	      exec($cmd);	 
	      echo ( "Done turning the device $Action<br>\n");     
     } else if ($Action == 'email') {
       $cmd = "python $workingDirectory/sendMail.py \"$Username\" \"$Subject\" \"$Body\"";
       echo ( "<hr><h2>CMD:</h2>$cmd<hr><BR>\n");
	      exec($cmd);	 
	      echo ( "Done sending email");       
     } else if ($Action == 'text') { 
       $email = textEmailAddress ($Provider);    
       $Username = "$Phone@$email";   
       sendEmail ($Username, '', $Message);     
     } else if ($Action == 'IM') {
       $cmd = "python $workingDirectory/broadcastCommand.py \"python $workingDirectory/xmpppy/send.py $Phone $Body\"";
       echo ( "<hr><h2>CMD:</h1>$cmd<hr><BR>\n");
       exec($cmd);
       echo ( "Done sending IM");      
     } else {
       echo ("Could not handle action: $Action <br>\n" );      
     }
   }      
   
   function mysql_fetch_assoc ($result) {
       $row = getResult ($result);
       // echo ("Returning row from mysql_fetch_assoc<br>\n" );
       return $row;
   } 
   
   function addWeight ($mac) {
     $sql = "Select * From sensors Where MAC='$mac'";
     $result = query ($sql);
     if ($row = mysql_fetch_assoc ($result)) {
        $Id = $row ['ID'];
        $sql = "Update sensors set Weight=$Id Where ID=$Id";
        echo ("$sql <br>\n" );
        $result = query ($sql);
        echo ("Weight updated for MAC:$mac<br>\n");        
     } else {
        echo ("Could not find MAC='$mac'<br>\n" );
     } 
   } 
   
   function resetWeights() {
     $sql = "Select * From sensors";
     $result = query ($sql);
     $count = 0;
     while ($row = mysql_fetch_assoc ($result)) {
       $MAC = $row ['MAC'];
       $TypeName = $row["TypeName"]; 
       if ($TypeName != 'timer') {
          addWeight ($MAC);
       }   
     }     
   } 
      
   function checkWeights() {
     $sql = "Select * From sensors";
     $result = query ($sql);
     $count = 0;
     $emptyWeight = false;
     while ($row = mysql_fetch_assoc ($result)) {
       $Weight = $row ['Weight'];
       $ID = $row ['ID'];
       $TypeName = $row["TypeName"]; 
       $MAC = $row["MAC"];       
       echo ( "[ID,TypeName,Weight]: [$ID,$TypeName,$Weight]<br>\n" );                 
       if ($TypeName != 'timer') {
          if ("$Weight" == "") { 
             $emptyWeight = true;
             break;
          } 
       } 
     }     
     if ($emptyWeight) { 
        resetWeights();
     } 
   } 
   
   
   function displaySensor ($ServerAddress, $Action) {
     $sql = "Select * From sensors";
     $result = query ($sql);
     $count = 0;
     $Message = "";
     while ($row = mysql_fetch_assoc ($result)) {
       $Value = $row ['Value'];           
       $TypeName = $row['TypeName'];
       $Nickname = $row['Nickname'];
       $Humidity = $row["Humidity"];
       if ($count == $Action) {            
         if ($TypeName == "timer") { 
           $Message = "^Server Ip Address:  $ServerAddress"; 
         } else {
           $Message = "^$TypeName";      
           $Message = padString ($Message, 21);
           $Message .= $Nickname;
           $Message = padString ($Message, 41);
           if ($TypeName == "dht11") {
             $Message .= "Humid:".$Humidity."% Temp:".$Value."F";
           } else if ($TypeName == "security") {
             if ($Value == '1') {
               $Message .= "Un-Locked";               
             } else {
               $Message .= "Locked";
             }
           } else { 
             $Message .= $Value;
           }   
           $Message = padString ($Message, 61);   
         }  
         break;
       }  
       $count = $count + 1; 
     }  
     return $Message;    
   }
   
   function padString ($msg, $len) {
     while (strlen ($msg) < $len) { 
       $msg .= " ";
     }  
     return $msg;     
   }
  
   function textEmailAddress ($provider) {
      global $providers;
      return $providers[$provider][1];    
   }
  
   function selectProviders ($provider) {  
      global $providers; 
      echo ("Provider: <Select name=\"Provider\">\n" );
      foreach ($providers as $key => $value) {
         if ($provider == $key) {
            echo "<option value=\"$key\" selected>$value[0]</option>\n";
         } else {
            echo "<option value=\"$key\">$value[0]</option>\n";          
         }   
      }
      echo ("</Select>\n");
   }

   function chooseCommand ($winCmd, $linuxCmd) {
       $cmd = $linuxCmd;
       return $cmd;
   }

   function sendHi () {
     sendEmail ( 'Hello', 'What is up?');
   }
   
   function sendElectronicMail ($subject, $message) {
     $name = 'Paul Richards'; // $_POST['name'];
     $email = 'richardspaulr1@gmail.com';
     //$message = $_POST['message'];
     $from = 'richardspaulr1@gmail.com'; 
     $to = 'Paulware@hotmail.com';
     // $subject = 'Customer Inquiry';
     $body = "From: $name\n E-Mail: $email\n Message:\n $message";

     //if ($_POST['submit']) {
         if (mail ($to, $subject, $body, $from)) { 
             echo '<p>Your message has been sent!</p>';
         } else { 
             echo '<p>Something went wrong, go back and try again!</p>'; 
         }
     //}
   }  
   
   function remoteAddr () {
       if (isset($_SERVER['HTTP_CLIENT_IP'])) {
           $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
       } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
           $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
       } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
           $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
       } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
           $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
       } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
           $ipaddress = $_SERVER['HTTP_FORWARDED'];
       } elseif (isset($_SERVER['REMOTE_ADDR'])) {
           $ipaddress = $_SERVER['REMOTE_ADDR'];
       } else {
           $ipaddress = 'UNKNOWN';
       }
       return $ipaddress;
   }   
   
   function mysql_query ( $sql ) {
       try {
           $conn = mysqli_connect ('localhost', 'root', 'pi', 'Paulware', 80);
           //echo "try query '$sql'<br>";
           
           $result = mysqli_query ($conn, $sql);
           if (!$result) {
              echo ("Error message: " );
              echo ("Unknown[" );
              echo ( mysqli_error ($conn) );
              echo ("]<br>" );
           }  
                     
           //echo 'Successfully: '.$sql.'<br>';
           //if ($result) {
           //   echo '<br>Got a return value';
           //}
       } 
       catch (Exception $e) {
          echo 'I had a problem';
          echo 'Error: mysqli_query could not execute: ' . $sql . ' err:' . $e->getMessage();
       }
       return $result;
   }
   
   function query ( $sql ) {
     $q = mysql_query ($sql);
     return $q;  
   }  
   
   function getResult ($result) {
     // changing while ($row = mysqli_fetch_assoc($result)) to:
     //          while ($row = $result->fetch_assoc())
     // finally 
     //          while ($row = getResult ($result)) 
     return $result->fetch_assoc();    
   }
   
   function MACtoIp($mac) {
     $sensor = findSensor($mac);
     $IpAddress = $sensor['IpAddress'];
     echo ("MAC: $mac yields: $IpAddress<br>\n" );
     return $IpAddress;     
   }
   
   function findSensor($mac) {
     $sql = "Select * From sensors Where MAC='$mac'";
     $result = query ( $sql);
     $value = mysql_fetch_assoc($result);
     return $value;
   }
  
   function findSensorId($id) {
     $sql = "Select * From sensors Where ID=$id";
     $result = query ( $sql);
     return mysql_fetch_assoc($result);          
   }
     
   function findMatch($user, $sensor) {
     $sql = "Select * From owners Where User='$user' and Sensor='$sensor'";
     $result = query ( $sql);
     return mysql_fetch_assoc($result);              
   }   
   
   function getParam ($name) {
      $value = $_GET["$name"];
      if ($value == "")
        $value = $_REQUEST["$name"];
       return $value;      
   }
   
   // Get lines ready for display
   function unescapeCharacters ($line) {
     $line = str_replace ('&#060;','<',$line);
     $line = str_replace ('&#062;','>',$line);
     $line = str_replace ('&#146;','\'',$line);
     $line = str_replace ('&#147;','"',$line);
	    $line = str_replace ('\r\n','<BR>',$line);
     //$line = str_replace ('&','$2$1$', $line);	 
	    $line = str_replace ('&#092;','\\',$line);
	    return $line;
   }
   
   function escapeCharacters ( $line ) {
     $line = str_replace ('<','&#060;',$line);
     $line = str_replace ('>','&#062;',$line);
     $line = str_replace ('\'','&#146;',$line);
     $line = str_replace ('"','&#147;',$line);
	    $line = str_replace ('\\','&#092;',$line);
     //$line = str_replace ('\r\n', '<BR>', $line );
     // Do not escape <BR>, <LI>, <UL>, or <OL> or others
     $line = str_replace ('&#060;BR&#062;','<BR>',$line );
     $line = str_replace ('&#060;LI&#062;','<LI>',$line );
     $line = str_replace ('&#060;UL&#062;','<UL>',$line );
     $line = str_replace ('&#060;OL&#062;','<OL>',$line );
     $line = str_replace ('&#060;/LI&#062;','</LI>',$line );
     $line = str_replace ('&#060;/UL&#062;','</UL>',$line );
     $line = str_replace ('&#060;/OL&#062;','</OL>',$line );
     $line = str_replace ('&#060;br&#062;','<br>',$line );
     $line = str_replace ('&#060;li&#062;','<li>',$line );
     $line = str_replace ('&#060;ul&#062;','<ul>',$line );
     $line = str_replace ('&#060;ol&#062;','<ol>',$line );
     $line = str_replace ('&#060;/li&#062;','</li>',$line );
     $line = str_replace ('&#060;/ul&#062;','</ul>',$line );
     $line = str_replace ('&#060;/ol&#062;','</ol>',$line );
     $line = str_replace ('&#060;b&#062;','<b>',$line );
     $line = str_replace ('&#060;/b&#062;','</b>',$line );
     $line = str_replace ('&#060;B&#062;','<B>',$line );
     $line = str_replace ('&#060;/B&#062;','</B>',$line );
	    $line = str_replace ('&#092;','\\',$line );

     return $line;
   }

   function findUser($name) {
     $sql = "Select * From users Where Username='$name'";
     $result = query ( $sql);
     return mysql_fetch_assoc($result);              
   }
   
   function getIsTriggered ($Event, $temperature, $TriggerValue, $humidity, $value, $currentValue, $TimeValue, $IButtonValue ) {
     echo "getIsTriggered Passing in IButtonValue: [$IButtonValue]<br>";
     // Get $isTriggered
     $isTriggered = false;     
     if ($Event == 'motion') {
       echo "<br>Motion does not yet qualify<br>\n";      
     } else if ($Event == 'colder') {
       if (floatval ($temperature) < floatval ($TriggerValue)) {
          $isTriggered = true;
       } else {
          echo ( "Event is not yet triggered, $temperature >= $TriggerValue");
       }
     } else if ($Event == 'warmer') {
       if (floatval ($temperature) > floatval ($TriggerValue)) {
          $isTriggered = true;
       } else {
          echo ( "Event is not yet triggered, $temperature <= $TriggerValue");
       }
     } else if ($Event == 'humidityAbove') {
       if (floatval ($humidity) > floatval ($TriggerValue)) {
          $isTriggered = true;
       } else {
          echo ( "Humidity event is not yet triggered, $humidity <= $TriggerValue");
       }
     } else if ($Event == 'humidityBelow') {
       if (floatval ($humidity) < floatval ($TriggerValue)) {
          $isTriggered = true;
       } else {
          echo ( "Humidity event is not yet triggered, $humidity >= $TriggerValue");
       }                  
     } else if ($Event == 'transitionLo') {      
         if ($value == '0') {
           if ($currentValue == '1') {
              $isTriggered = true;
           } else {
              echo ("Not transitioning low because currentValue is not 1...currentValue: $currentValue");
           }
         } else {
           echo ("Action not triggered because value $value != 0");
         }      
     } else if ($Event == 'sensorLo') {
         if (intval ($value) == 0) {
           $isTriggered = true;
         }
     } else if ($Event == 'transitionHi') {      
         if ($value == '1') {
           if ($currentValue == '0') {
              $isTriggered = true;
           } else {
              echo ("Action not triggered because currentValue: $currentValue != 0");
           }
         }       
     } else if ($Event == 'sensorHi') {
         if (intval ($value) == 1) {
           $isTriggered = true;
         }
     } else if ($Event == 'water') {
       if ($value == 1) {
          echo "<br>Action does not qualify because water was not detected yet";
       } else { // Water was detected
          $isTriggered = true;
       }
     } else if ($Event == 'iButton') {       
       if ($value == $IButtonValue) {
          echo "<br> ibutton value: [$value] == [$IButtonValue]   TRIGGERED=YES<br>";
          $isTriggered = true;
       } else {
          echo "<br> ibutton value: [$value] <> [$IButtonValue]<br>";
       }         
     } else if ($Event == 'time') {
       $t = date ('H:i', time());
       if ($t == $TimeValue) {
          $isTriggered = true;
       } else {
         echo "Current time: $t <> $TimeValue<br>\n";
       }  
     } else {
       if (substr ($Event, 0, 4) == 'gpio') { 
         $which = substr ($Event,4,2);
         $transition = substr ($Event, 6);
         echo "<br> I got a possible gpio[$which], $transition, value: $value, currentValue: $currentValue";
         if ($transition == 'TransitionLo') {
           $current = substr ($currentValue, 6, 1);
           if ($current == "0") { 
             echo ("Cannot transition lo, already lo" );
           } else { 
             $val = substr ($value, 6, 1); 
             if ($val == "1") { 
               echo ( "Cannot transition lo, now: hi" );
             } else {
               echo ( "TransitionLo!" );
               $isTriggered = true;
             }
           } 
         } else if ($transition == 'TransitionHi') {
           $current = substr ($currentValue, 6, 1);
           if ($current == "1") { 
             echo ("Cannot transition Hi, already Hi" );
           } else { 
             $val = substr ($value, 6, 1); 
             if ($val == "0") { 
               echo ( "Cannot transition Hi, now: Lo" );
             } else {
               echo ( "TransitionHi!" );
               $isTriggered = true;
             }
           } 
         }
       } else { 
         echo "<br>Event: $Event is not handled<br>\n";
       }  
     } 
     return $isTriggered;
   }  
?>