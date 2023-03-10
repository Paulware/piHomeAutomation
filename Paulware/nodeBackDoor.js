var exec  = require ('child_process').exec;
var dgram = require ("dgram");
var fs    = require ('fs');
var PORT = 4444; // Port we will listen at

commandLog = 'nodeCommand.log';

function appendFile ( filename, msg) {
   fs.open ( filename, 'a+', function (err,data) {
     if (err) {
       console.log ( 'err: ' + err);
     } else {
       console.log ( 'writing ' + msg + ' to ' + filename);
       fs.write (data, msg, 0, 'content length', null, 
       function (err) {
         if (err)
           console.log ( 'Err1: ' + err);
       });
       fs.close (data, function() {
       });                 
     }    
   }); 
}

// String enhancements
if(typeof(String.prototype.trim) === "undefined") {
     String.prototype.trim=function() {
         return String(this).replace(/^\s+|s+$/g, '');
     }
}

var listener = dgram.createSocket ( "udp4");
listener.on ("message",function (command, rinfo) {
  console.log ("Server got: " + command + " from " + rinfo.address + ":" + rinfo.port);
  
  appendFile ( commandLog, command + '\n');   
  
  child = exec(command,
     function (error, stdout, stderr) {
        console.log ( command + '> ' + stdout)        
        listener.send (stdout, 0, stdout.length, rinfo.port, rinfo.address, function() {
           console.log ( 'Sent: ' + stdout + ' to [' + rinfo.address + ':' + rinfo.port + ']');
        });
        if (stderr.trim() != '') {            
           console.log ('stderr: ' + stderr);
        }   
        if (error !== null) {
           // appendFile errorlog error?
           console.log('exec error: ' + error);
        }
     }
  );      
  /* 
  child.on ( "exit", function () {
     listener.send ( 'Done! Here', 0, 10, rinfo.port, rinfo.address , function() {
        console.log ( 'Sent Done! Here to [' + rinfo.address + ',' + rinfo.port + ']');
     });
  });
  */  
});

listener.on ("listening", function() {
  var address = listener.address ();
  console.log ( "listening at " + address.address + ":" + address.port);  
});
listener.bind (PORT); // bind port 3333, sensors also pick this port (on their ip addresses)

