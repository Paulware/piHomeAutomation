<?php
  include "common.inc";
  include "common.php";
  function echoOption ( $firstPart,$isSelected, $lastPart) {
    if ($isSelected) {
       echo ("<option $firstPart selected $lastPart</option>\n");
    } else {
       echo ("<option $firstPart $lastPart</option>\n");
    }
  }    

  $Sensor = getParam ("Sensor");  
  $ID = getParam ("ID");
  $Event = '';
  $Phone = '';
  $Provider = '';
  $Action = '';
  $Message='';
  $Username = '';
  $Subject='';
  $Body='';
  $AffectedMAC='';
  $TimeValue = '';
  $TriggerValue = '';
  $FrequencySeconds = '0';
  $JabberRecipient = '';
  $JabberMessage = '';
  $IButtonValue = '';
  if ($ID > 0) {
    $sql = "Select * From actions where ID=$ID";
    $result = query ($sql);
    if ($result) {
      $row              = mysql_fetch_assoc ($result); 
      $Sensor           = $row["Sensor"];
      $Event            = $row["Event"];
      $Phone            = $row["Phone"];
      $Provider         = $row["Provider"];
      $Action           = $row["Action"];
      $Message          = $row["Message"];
      $Username         = $row["Username"];
      $Subject          = $row["Subject"];
      $AffectedMAC      = $row["AffectedMAC"];
      $Body             = $row["Body"];
      $TimeValue        = $row["TimeValue"];
      $TriggerValue     = $row["TriggerValue"];
      $FrequencySeconds = $row["FrequencySeconds"];
      $IButtonValue     = $row["IButtonValue"];
      $JabberRecipient = $Phone;
      $JabberMessage   = $Body;
    }
  }    
?>

<html>
<head> <Title>Edit Sensor Action 1.0</Title>
<Script>

   function modAction (ID) {
       getVariables();
       url = 'addAction.php?ID=' + ID + '&Sensor=' + Sensor + '&Action=' + Action + 
             '&Event=' + Event + '&Phone=' + Phone + '&Provider=' + Provider + 
             '&Username=' + Username + '&Message=' + Message + '&Subject=' + Subject + 
             '&Body=' + Body + '&AffectedMAC=' + AffectedMAC + '&TriggerValue=' + 
             TriggerValue + '&PhotoUsername=' + PhotoUsername + '&PhotoSubject=' + 
             PhotoSubject + '&PhotoDevice=' + PhotoDevice + '&TimeValue='+TimeValue +
             '&FrequencySeconds=' + FrequencySeconds + '&IButtonValue='+IButtonValue;
       window.location.href=url;
   }

</Script>
</head>
<body onload="changeSelection();">

<?php
   
  if ($ID == 0) {
     echo ("Add a new action.<br>\n");      
  } else {
     echo ("Modify an existing action.<br>\n");      
  }

echo ("When\n");
echo ("<Select name=\"Event\" onchange=\"changeEvent();\">\n");
echoOption ("value=\"motion\" ",$Event == 'motion',">Motion Detected");
echoOption ("value=\"colder\" ",$Event == 'colder',">Temperature Below");
echoOption ("value=\"warmer\" ",$Event == 'warmer',">Temperature Above");
echoOption ("value=\"humidityAbove\" ", $Event == 'humidityAbove', ">Humidity Above");
echoOption ("value=\"humidityBelow\" ", $Event == 'humidityBelow', ">Humidity Below");
echoOption ("value=\"water\" ",$Event == 'water',">Water Detected");
echoOption ("value=\"time\" ",$Event == 'time',">Time is");
echoOption ("value=\"sensorLo\" ",$Event == 'sensorLo',">Sensor Low");
echoOption ("value=\"sensorHi\" ",$Event == 'sensorHi',">Sensor High");
echoOption ("value=\"iButton\" ",$Event == 'iButton',">iButton detected");
echoOption ("value=\"transitionLo\" ",$Event == 'transitionLo',">Transitions to Low");
echoOption ("value=\"gpio00TransitionLo\" ",$Event == 'gpio00TransitionLo',">Gpio pin 0 Transitions to Low");
echoOption ("value=\"gpio01TransitionLo\" ",$Event == 'gpio01TransitionLo',">Gpio pin 1 Transitions to Low");
echoOption ("value=\"gpio02TransitionLo\" ",$Event == 'gpio02TransitionLo',">Gpio pin 2 Transitions to Low");
echoOption ("value=\"gpio03TransitionLo\" ",$Event == 'gpio03TransitionLo',">Gpio pin 3 Transitions to Low");
echoOption ("value=\"gpio04TransitionLo\" ",$Event == 'gpio04TransitionLo',">Gpio pin 4 Transitions to Low");
echoOption ("value=\"gpio05TransitionLo\" ",$Event == 'gpio05TransitionLo',">Gpio pin 5 Transitions to Low");
echoOption ("value=\"gpio06TransitionLo\" ",$Event == 'gpio06TransitionLo',">Gpio pin 6 Transitions to Low");
echoOption ("value=\"gpio07TransitionLo\" ",$Event == 'gpio07TransitionLo',">Gpio pin 7 Transitions to Low");
echoOption ("value=\"gpio08TransitionLo\" ",$Event == 'gpio08TransitionLo',">Gpio pin 8 Transitions to Low");
echoOption ("value=\"gpio09TransitionLo\" ",$Event == 'gpio09TransitionLo',">Gpio pin 9 Transitions to Low");
echoOption ("value=\"transitionHi\" ",$Event == 'transitionHi',">Transitions to High");
echoOption ("value=\"gpio00TransitionHi\" ",$Event == 'gpio00TransitionHi',">Gpio pin 0 Transitions to High");
echoOption ("value=\"gpio01TransitionHi\" ",$Event == 'gpio01TransitionHi',">Gpio pin 1 Transitions to High");
echoOption ("value=\"gpio02TransitionHi\" ",$Event == 'gpio02TransitionHi',">Gpio pin 2 Transitions to High");
echoOption ("value=\"gpio03TransitionHi\" ",$Event == 'gpio03TransitionHi',">Gpio pin 3 Transitions to High");
echoOption ("value=\"gpio04TransitionHi\" ",$Event == 'gpio04TransitionHi',">Gpio pin 4 Transitions to High");
echoOption ("value=\"gpio05TransitionHi\" ",$Event == 'gpio05TransitionHi',">Gpio pin 5 Transitions to High");
echoOption ("value=\"gpio06TransitionHi\" ",$Event == 'gpio06TransitionHi',">Gpio pin 6 Transitions to High");
echoOption ("value=\"gpio07TransitionHi\" ",$Event == 'gpio07TransitionHi',">Gpio pin 7 Transitions to High");
echoOption ("value=\"gpio08TransitionHi\" ",$Event == 'gpio08TransitionHi',">Gpio pin 8 Transitions to High");
echoOption ("value=\"gpio09TransitionHi\" ",$Event == 'gpio09TransitionHi',">Gpio pin 9 Transitions to High");
echo ( "</Select>\n");

// echo (" Note Event: [$Event]<br>\n" );
if ($Event == 'time') {
  echo ("<div id=\"divTime\"> HH:MM<input name=\"TimeValue\" value=\"$TimeValue\"></div>\n");
} else {
  echo ("<div id=\"divTime\" style=\"display:none;\"> HH:MM<input name=\"TimeValue\" value=\"$TimeValue\"></div>\n"); 
}
if ($Event == 'iButton') {
  echo ("<div id=\"divIButton\">with hex address: XX:XX:XX:XX:XX:XX:XX:XX<input name=\"iButton\" value=\"$IButtonValue\"></div>\n");
} else {
  echo ("<div id=\"divIButton\" style=\"display:none;\">with hex address XX:XX:XX:XX:XX:XX:XX:XX<input name=\"iButton\" value=\"$IButtonValue\"></div>\n"); 
}
echo ("<div id=\"divTrigger\" style=\"display:none;\">Trigger Value: <input name=\"TriggerValue\" value=\"$TriggerValue\"></div>\n");
echo ("<Select name=\"Action\" onchange=\"changeSelection();\">\n");
echoOption ("value=\"email\" ",$Action == "email", ">Send an Email");
echoOption ("value=\"text\" ", $Action == "text",  ">Send a text");
echoOption ("value=\"IM\" ",   $Action == "IM",    ">Send an IM");
echoOption ("value=\"gpio06HI\" ", $Action == "gpio06HI",   ">Turn Sonoff On" );
echoOption ("value=\"gpio06LO\" ", $Action == "gpio06LO",   ">Turn Sonoff Off" );
echoOption ("value=\"gpio00HI\" ", $Action == "gpio00HI",   ">Set gpio0 of a device HIGH" );
echoOption ("value=\"gpio01HI\" ", $Action == "gpio01HI",   ">Set gpio1 of a device HIGH" );
echoOption ("value=\"gpio02HI\" ", $Action == "gpio02HI",   ">Set gpio2 of a device HIGH" );
echoOption ("value=\"gpio03HI\" ", $Action == "gpio03HI",   ">Set gpio3 of a device HIGH" );
echoOption ("value=\"gpio04HI\" ", $Action == "gpio04HI",   ">Set gpio4 of a device HIGH" );
echoOption ("value=\"gpio05HI\" ", $Action == "gpio05HI",   ">Set gpio5 of a device HIGH" );
echoOption ("value=\"gpio06HI\" ", $Action == "gpio06HI",   ">Set gpio6 of a device HIGH" );
echoOption ("value=\"gpio07HI\" ", $Action == "gpio07HI",   ">Set gpio7 of a device HIGH" );
echoOption ("value=\"gpio08HI\" ", $Action == "gpio08HI",   ">Set gpio8 of a device HIGH" );
echoOption ("value=\"gpio09HI\" ", $Action == "gpio09HI",   ">Set gpio9 of a device HIGH" );
echoOption ("value=\"gpio00LO\" ", $Action == "gpio00LO",  ">Set gpio0 of a device LOW" );
echoOption ("value=\"gpio01LO\" ", $Action == "gpio01LO",  ">Set gpio1 of a device LOW" );
echoOption ("value=\"gpio02LO\" ", $Action == "gpio02LO",  ">Set gpio2 of a device LOW" );
echoOption ("value=\"gpio03LO\" ", $Action == "gpio03LO",  ">Set gpio3 of a device LOW" );
echoOption ("value=\"gpio04LO\" ", $Action == "gpio04LO",  ">Set gpio4 of a device LOW" );
echoOption ("value=\"gpio05LO\" ", $Action == "gpio05LO",  ">Set gpio5 of a device LOW" );
echoOption ("value=\"gpio06LO\" ", $Action == "gpio06LO",  ">Set gpio6 of a device LOW" );
echoOption ("value=\"gpio07LO\" ", $Action == "gpio07LO",  ">Set gpio7 of a device LOW" );
echoOption ("value=\"gpio08LO\" ", $Action == "gpio08LO",  ">Set gpio8 of a device LOW" );
echoOption ("value=\"gpio09LO\" ", $Action == "gpio09LO",  ">Set gpio9 of a device LOW" );
// echoOption ("value=\"photo\"", $Action == "photo", ">Send a photo(TBD)");
echo ("</Select>\n");

echo ("<div id=\"divEmail\">\n" );
echo ("To:<input name=\"Username\" value=\"$Username\"><br>\n" );
echo ("Subject: <input name=\"Subject\" value=\"$Subject\"><br>\n" );
echo ("Body: <input name=\"Body\" value=\"$Body\"><br>\n" );
echo ("</div>\n" );

?>

<div id="divPhoto" style="display:none;">
To:<input name="PhotoUsername"><br>
Subject: <input name="PhotoSubject"><br>
Trigger Device: <input name="PhotoDevice"><br>
</div>

<?php
 
echo ("<div id=\"divText\" style=\"display:none;\">\n" );
selectProviders($Provider);

echo ("Phone Number: <input name=\"Phone\" value=\"$Phone\"><br>\n" );
echo ("Message: <input name=\"Message\" size=\"120\" value=\"$Message\"><br>\n" );
echo ("</div>\n");

echo ("<div id=\"divIM\" style=\"display:none;\">\n");
echo ("Jabber receiver account: <input name=\"JabberRecipient\" value=\"$JabberRecipient\"><br>\n" );
echo ("Message: <input name=\"JabberMessage\" size=\"120\" value=\"$JabberMessage\"><br>\n" );
echo ("</div>\n");

echo ("<div id=\"divOn\" style=\"display:none;\">\n");
echo ("Set gpio pin high of device with <br>\n");

echo ("</div>\n" );
echo ("<div id=\"divOff\" style=\"display:none;\">\n" );
echo ("Set gpio pin low of device with <br>\n" );
echo ("</div>\n" );
echo ("<div id=\"MAC\" style=\"display:none;\">\n" );
echo ("MAC: <input name=\"AffectedMAC\" value=\"$AffectedMAC\"><br>\n" );
echo ("</div>\n");

echo ("<Script>\n" );

echo ("\n   var Sensor = $Sensor;\n" );
echo ("\n   var ID = $ID;\n");  
?>

  var Action;
  var Event;
  var Phone;
  var Username;
  var Message;
  var Subject;
  var Body;
  var Provider;
  var AffectedMAC
  var TriggerValue;
  var PhotoUsername;
  var PhotoSubject;
  var PhotoDevice;
  var JabberMessage;
  var JabberRecipint;
  var IButtonValue;

  function getVariables() {
     Action           = document.all.Action.value;   
     Event            = document.all.Event.value;
     Phone            = document.all.Phone.value;
     Username         = document.all.Username.value;
     Message          = escape(document.all.Message.value);
     Subject          = escape(document.all.Subject.value);
     Body             = escape(document.all.Body.value);
     Provider         = document.all.Provider.value;
     AffectedMAC      = document.all.AffectedMAC.value;
     TriggerValue     = document.all.TriggerValue.value;
     TimeValue        = document.all.TimeValue.value;
     FrequencySeconds = document.all.FrequencySeconds.value;
     JabberMessage    = document.all.JabberMessage.value;
     JabberRecipient  = document.all.JabberRecipient.value;
     IButtonValue     = document.all.iButton.value
     if (Action=="photo") {
        Username = document.all.PhotoUsername.value;
        Subject = document.all.PhotoSubject.value;
        Body = "Photo triggered by writing to this sensor" + AffectedMAC;
        Action = 'email';
     } else if (Action == "IM") {
        Body = document.all.JabberMessage.value;
        Phone = document.all.JabberRecipient.value;        
     } 
  }

  function testAction () {
     getVariables();
     window.location.href = 'testAction.php?Sensor=' + Sensor + '&Action=' + Action + '&Event=' + Event + 
       '&Phone=' + Phone + '&Username=' + Username + '&Message=' + Message + '&Subject=' + Subject + '&Body=' + 
       Body + '&Provider=' + Provider + '&AffectedMAC=' + AffectedMAC + '&TriggerValue=' + TriggerValue;             
  }

  function showTemp() {
     document.all.divTrigger.style.display = 'none';     
      
     if (document.all.Event.value == 'warmer') {
        document.all.divTrigger.style.display = 'block';     
     } else if (document.all.Event.value == 'colder') {
        document.all.divTrigger.style.display = 'block';
     } else if (document.all.Event.value == 'humidityAbove') {
        document.all.divTrigger.style.display = 'block';
     } else if (document.all.Event.value == 'humidityBelow') {
        document.all.divTrigger.style.display = 'block';
     }
  }

  function changeEvent() {   
     showTemp();
     document.all.divTime.style.display = 'none';
     document.all.divIButton.style.display = 'none';
	    value = document.all.Event.value;
     if (value=='time') {
        document.all.divTime.style.display='block';     
     } else if (value == 'iButton' ) {
        document.all.divIButton.style.display = 'block';
     }       
  }
  
  function troubleShoot() {
     window.location.href = 'troubleShoot.php';
  }

  function changeSelection() {   
     document.all.divText.style.display    = 'none';
     document.all.divEmail.style.display   = 'none';
     document.all.divOn.style.display      = 'none';
     document.all.divOff.style.display     = 'none';
     document.all.MAC.style.display        = 'none';
	     
	    value = document.all.Action.value;
     if (value == 'email') {
        document.all.divEmail.style.display = 'block';
     } else if (value == 'text') {
        document.all.divText.style.display = 'block';
     } else if (value.substring(6,8) == 'HI') {
        document.all.divOn.style.display = 'block';      
        document.all.MAC.style.display = 'block';
     } else if (value.substring(6,8) == 'LO') {
        document.all.divOff.style.display = 'block';         
        document.all.MAC.style.display = 'block';
     } else if (value=='photo') {      
        document.all.divPhoto.style.display='block';
     } else if (value=='IM') {
        document.all.divIM.style.display='block';      
     }
     showTemp();
     
  }
  // alert ( 'ready'); // For debugging only
</Script>
<hr>How often would like the action to take place?<br>
<?php
  echo ("Action Frequency (seconds):<input name=\"FrequencySeconds\" value=\"$FrequencySeconds\">Note: 0=take action whenever sensor reports<br>");
  echo ("<hr>\n");
  //echo ("<br>TimeValue: $TimeValue<br>\n");
  if ($ID == 0) {
     echo ("<input type=\"button\" value=\"Add Action\" onclick=\"modAction(0);\">\n");      
  } else {
     echo ("<input type=\"button\" value=\"Save Action\" onclick=\"modAction($ID);\">\n");      
  }
?>
<hr>
<input type="button" value="TroubleShooting" onclick="troubleShoot();"><br>
<input type="button" value="Test" onclick="testAction();"><br>
<hr>
<input type="button" value="Cancel" onclick="window.location.href='index.php';">
</body>
</html>