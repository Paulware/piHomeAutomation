<?php
  include "common.inc";
  include "common.php";
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
  if ($ID > 0) {
    $sql = "Select * From actions where ID=$ID";
    $result = query ($sql);
    if ($result) {
      $row = mysql_fetch_assoc ($result); 
      $Sensor = $row["Sensor"];
      $Event = $row["Event"];
      $Phone = $row["Phone"];
      $Provider = $row["Provider"];
      $Action = $row["Action"];
      $Message = $row["Message"];
      $Username = $row["Username"];
      $Subject=$row["Subject"];
      $Body=$row["Body"];
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
             '&Body=' + Body + '&MACOn=' + MACOn + '&MACOff=' + MACOff + '&TriggerValue=' + 
             TriggerValue + '&PhotoUsername=' + PhotoUsername + '&PhotoSubject=' + 
             PhotoSubject + '&PhotoDevice=' + PhotoDevice;
       window.location.href=url;
   }
</Script>
</head>
<body>

<?php
  if ($ID == 0) {
     echo ("Add a new action.<br>\n");      
  } else {
     echo ("Modify an existing action.<br>\n");      
  }

echo ("When\n");
echo ("<Select name=\"Event\" onchange=\"changeEvent();\">\n");
if ($Event == 'motion') {
    echo ("<option value=\"motion\" selected>Motion Detected</option>\n");
} else {
    echo ("<option value=\"motion\">Motion Detected</option>\n");    
}
if ($Event == 'colder') {
    echo ("<option value=\"colder\" selected>Temperature Below</option>\n");
} else {
    echo ("<option value=\"colder\">Temperature Below</option>\n");
}
if ($Event == 'warmer') {
    echo ("<option value=\"warmer\" selected>Temperature Above</option>\n");
} else {
    echo ("<option value=\"warmer\">Temperature Above</option>\n");
}
if ($Event == 'water') {
    echo ("<option value=\"water\" selected>Water Detected</option>\n");
} else {
    echo ("<option value=\"water\">Water Detected</option>\n");
}

echo ( "</Select>\n");


echo ("<div id=\"divTrigger\" style=\"display:none;\"><input name=\"TriggerValue\"> degrees Fahrenheit</div>\n");
echo ("<Select name=\"Action\" onchange=\"changeSelection();\">\n");
if ($Action == 'email') {
   echo ("<option value=\"email\" selected>Send an Email</option>\n");    
} else {
   echo ("<option value=\"email\">Send an Email</option>\n");    
}
if ($Action == 'text') {
   echo ("<option value=\"text\" selected>Send a text</option>\n");    
} else {
   echo ("<option value=\"text\">Send a text</option>\n");    
}
if ($Action == 'on') {
   echo ("<option value=\"text\" selected>Send a text</option>\n");    
} else {
   echo ("<option value=\"text\">Send a text</option>\n");    
}
if ($Action == 'off') {
   echo ("<option value=\"off\" selected>Turn off a device</option>\n");    
} else {
   echo ("<option value=\"off\">Turn off a device</option>\n");    
}
if ($Action == 'photo') {
   echo ("<option value=\"photo\" selected>Send a photo(TBD)</option>\n");    
} else {
   echo ("<option value=\"photo\">Send a photo(TBD)</option>\n");    
}
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
echo ("Provider: <Select name=\"Provider\">\n" );
if ($Provider == 'alltel') {
   echo ( "<option value=\"alltel\" selected>Alltel</option>\n" ); 
} else {
   echo ( "<option value=\"alltel\">Alltel</option>\n" );
}
if ($Provider == 'att') {
   echo ( "<option value=\"att\" selected>AT&T</option>\n" );
} else {
   echo ( "<option value=\"att\">AT&T</option>\n" );
}
if ($Provider == 'boost') {
   echo ( "<option value=\"boost\" selected>Boost Mobile</option>\n" );
} else {
   echo ( "<option value=\"boost\">Boost Mobile</option>\n" );
}
if ($Provider == 'cricket') {
   echo ( "<option value=\"cricket\" selected>Cricket</option>\n" );
} else {
   echo ( "<option value=\"cricket\">Cricket</option>\n" );
}
if ($Provider == 'metro') {
   echo ( "<option value=\"metro\" selected>Metro PCS</option>\n" );
} else {
   echo ( "<option value=\"metro\">Metro PCS</option>\n" );
}
if ($Provider == 'nextel') {
   echo ( "<option value=\"nextel\" selected>Nextel</option>\n" );
} else {
   echo ( "<option value=\"nextel\">Nextel</option>\n" );
}
if ($Provider == 'qwest') {
   echo ( "<option value=\"qwest\" selected>Qwest</option>\n" );
} else {
   echo ( "<option value=\"qwest\">Qwest</option>\n" );
}
if ($Provider == 'tmobile') {
   echo ( "<option value=\"tmobile\" selected>T-Mobile</option>\n" );
} else {
   echo ( "<option value=\"tmobile\">T-Mobile</option>\n" );
}

if ($Provider == 'sprintpcs') {
   echo ( "<option value=\"sprintpcs\" selected>Sprint PCS</option>\n" );
} else {
   echo ( "<option value=\"sprintpcs\">Sprint PCS</option>\n" );
}
if ($Provider == 'sprintpm') {
   echo ( "<option value=\"sprintpm\" selected>Sprint PM</option>\n" );
} else {
   echo ( "<option value=\"sprintpm\">Sprint PM</option>\n" );
}
if ($Provider == 'suncom') {
   echo ( "<option value=\"suncom\" selected>suncom</option>\n" );
} else {
   echo ( "<option value=\"suncom\">suncom</option>\n" );
}
if ($Provider == 'uscellular') {
   echo ( "<option value=\"uscellular\" selected>US Cellular</option>\n" );
} else {
   echo ( "<option value=\"uscellular\">US Cellular</option>\n" );
}
if ($Provider == 'verizon') {
   echo ( "<option value=\"verizon\" selected>Verizon</option>\n" );
} else {
   echo ( "<option value=\"verizon\">Verizon</option>\n" );
}
if ($Provider == 'virgin') {
   echo ( "<option value=\"virgin\" selected>Virgin Mobile</option>\n" );
} else {
   echo ( "<option value=\"virgin\">Virgin Mobile</option>\n" );
}
echo ("</Select>\n" );
echo ("Phone Number: <input name=\"Phone\" value=\"$Phone\"><br>\n" );
echo ("Message: <input name=\"Message\" size=\"120\" value=\"$Message\"><br>\n" );
?>

</div>
<div id="divOn" style="display:none;">
Turn on a device<br>
MAC: <input name="MACOn"><br>
</div>
<div id="divOff" style="display:none;">
Turn off a device<br>
MAC: <input name="MACOff"><br>
</div>

<Script>
  <?php
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
  var MACOn;
  var MACOff;
  var TriggerValue;
  var PhotoUsername;
  var PhotoSubject;
  var PhotoDevice;
  
  function getVariables() {
     Action=document.all.Action.value;   
     Event=document.all.Event.value;
     Phone=document.all.Phone.value;
     Username=document.all.Username.value;
     Message=escape(document.all.Message.value);
     Subject=escape(document.all.Subject.value);
     Body=escape(document.all.Body.value);
     Provider=document.all.Provider.value;
     MACOn=document.all.MACOn.value;
     MACOff=document.all.MACOff.value;
     TriggerValue=document.all.TriggerValue.value;
     if (Action=="photo") {
        Username = document.all.PhotoUsername.value;
        Subject = document.all.PhotoSubject.value;
        MACOn = document.all.PhotoDevice.value;
        Body = "Photo triggered by " + MACOn;
        Action = 'email';
     }   
  }
  function testAction () {
     getVariables();
     window.location.href = 'testAction.php?Sensor=' + Sensor + '&Action=' + Action + '&Event=' + Event + 
             '&Phone=' + Phone + '&Username=' + Username + '&Message=' + Message + '&Subject=' + Subject + '&Body=' + 
             Body + '&Provider=' + Provider + '&MACOn=' + MACOn + '&MACOff=' + MACOff + '&TriggerValue=' + TriggerValue;             
  }
  function showTemp() {
     document.all.divTrigger.style.display = 'none';
     if (document.all.Event.value == 'warmer') {
        document.all.divTrigger.style.display = 'block';     
     } else if (document.all.Event.value == 'colder') {
        document.all.divTrigger.style.display = 'block';
     }      
  }
  function changeEvent() {
     showTemp();
  }
  function changeSelection() {
     document.all.divText.style.display    = 'none';
     document.all.divEmail.style.display   = 'none';
     document.all.divOn.style.display      = 'none';
     document.all.divOff.style.display     = 'none';
	 
	 value = document.all.Action.value;
     
     if (document.all.Action.value == 'email') {
        document.all.divEmail.style.display = 'block';
     } else if (document.all.Action.value == 'text') {
        document.all.divText.style.display = 'block';
     } else if (document.all.Action.value == 'on') {
        document.all.divOn.style.display = 'block';      
     } else if (document.all.Action.value == 'off') {
        document.all.divOff.style.display = 'block';     
     } else if (document.all.Action.value=='photo') {      
        document.all.divPhoto.style.display='block';
     }
     showTemp();
  }
  changeSelection();
</Script>
<?php
  if ($ID == 0) {
     echo ("<input type=\"button\" value=\"Add Action\" onclick=\"modAction(0);\">\n");      
  } else {
     echo ("<input type=\"button\" value=\"Modify Action\" onclick=\"modAction($ID);\">\n");      
  }
?>
<br>
<input type="button" value="Test" onclick="testAction();">
</body>
</html>