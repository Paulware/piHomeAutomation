<html>
<body>
<script>
   function selectTimeZone(value) {
      // alert ( 'Select this time zone: ' + value );
      window.location.href = 'selectTimeZone.php?zone=' + value;
   }
   function saveUsernamePassword() {
       var username = encodeURIComponent(document.all.username.value);
       var password = encodeURIComponent(document.all.password.value);
       //alert ( 'Save username password [' + username + ',' + password + ']');
       window.location.href = 'updateGmailConfig.php?username=' + username + '&password=' + password;
   }
   function saveSSIDPassword() {
       var ssid = encodeURIComponent(document.all.ssid.value);
       var passphrase = encodeURIComponent(document.all.passphrase.value);
       if (ssid.length < 8) { 
          alert ( 'Length of ssid must be at least 8 characters' );
       } else if ((passphrase.length > 0) && (passphrase.length < 8)) {
          alert ( 'Passphrase must be blank (open) or be at least 8 characters in length' );
       } else {
          window.location.href = 'updateSSID.php?ssid=' + ssid + '&passphrase=' + passphrase;
       }
   }
   function saveJabber() {

       var jabberTx = encodeURIComponent(document.all.jabberTx.value);
       var jabberPassword = encodeURIComponent(document.all.jabberPassword.value);
       
       window.location.href = 'updateJabberConfig.php?jabberTx=' + jabberTx + '&jabberPassword=' + jabberPassword;
   }
</script>

<p><center style="font-size:200%">Home Automation Center</center></p>
<hr><center>
<center>

<table>
  <tr width="80%">
  <td align="left">
		<b><h2>Configuration Page: </h2></b>
  		<ul>
     <h2>Time/Time Zone</h2>
     <ul>
         <li>Current Time Zone: 
         <?php
           $currentTimeZone = date_default_timezone_get();
           echo $currentTimeZone;
           echo "<br>To change the time zone <ul><li>Login to raspberry pi with putty</li><li>Enter the command: raspi-config and select internationalisation options</li></ul>\n";
           /*
           $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
           echo ("Select TimeZone<Select name=\"timezone\" onchange=\"selectTimeZone(this.value);\">\n" );
           foreach ($tzlist as $tz) {
             if ($tz == $currentTimeZone) {
               echo ("<option value=\"$tz\" selected>$tz</option>\n" );
             } else {
               echo ("<option value=\"$tz\">$tz</option>\n" );
             }  
           }
           echo ("</Select>\n" );
           */
         ?>
         </li>
         <li>Current time:
         <?php
            $hour = date('H');
            $minute = date('i');            
            echo (" $hour:$minute <br>\n");            
         ?>
         </li>
     </ul>
     <h2>SSID Configuration (/etc/hostapd/hostapd.conf)</h2>
             <table> 
             <tr><td>SSID:</td><td><input name="ssid"></td></tr>
             <tr><td>passphrase:</td><td><input name="passphrase" type="password"></td></tr>
             <tr><td><input type="button" value="Save SSID Info" onclick="saveSSIDPassword();"></td><td>&nbsp;</td></tr>
             </table>
     <h2>Gmail Configuration</h2>
     <ul>
        <li>Get your free gmail account <a href="http://www.gmail.com">here</a></li>
        <li><i>Tell server your gmail username and password for sending email</i><br>
               Note: Do not put "@" or "gmail.com" in the username
             <table> 
             <tr><td>Username:</td><td><input name="username"></td></tr>
             <tr><td>Password:</td><td><input name="password" type="password"></td></tr>
             <tr><td><input type="button" value="Save Gmail Info" onclick="saveUsernamePassword();"></td><td>&nbsp;</td></tr>
             </table>
        </li>
        <p>
        <li>Gmail requires that it be configured to "allow non-secure apps to send email"<br>   To do this
          <ul>
             <li>Log in to your gmail account in your web-browser</li>
             <li>Then navigate to:<a href="https://www.google.com/settings/security/lesssecureapps">https://www.google.com/settings/security/lesssecureapps</a> <br>select: turn on</li>
          </ul>
        </li>
     </ul>
     <p>  
     <script>
     /*      
     <h2>IM Configuration</h2>
     <ul>     
        <li><i>Get an IM Client <a href="http://xmpp.org/software/clients.html">here</a></i></li>
        <p>     
        <li><i>Get free Jabber account <a href="https://xmpp.net/">here</a></i></li>
        <p>
        <li><i>Setup Jabber client to send IMs</i><br>
            <table>
            <tr><td>Username@JabberServer</td><td><input name="jabberTx"></td></tr>
            <tr><td>Sending Account Password</td><td><input type="password" name="jabberPassword"></td></tr> 
            <tr><td><input type="button" value="Save Jabber Info" onclick="saveJabber();"></td></tr>
            </table>
        </li>    
     </ul>
     <p>
     */
     </script>
  		</ul>
	</td>
</tr>
</table>
  
  <table width="100%"
  		<tr><td><hr><br></td></tr>
  </table>
  		  
<table width="100%">

</table>
  

<table>
	<tr>
		<td>
			<input type="button" value="back" onclick="window.location.href='index.php';">
		</td>
	</tr>
</table>

</table>
</center>
 </body>
 </html>