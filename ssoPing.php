<html>
<!--
   Copyright 2015 Jason A. Everling

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
-->
<!--

    For Connecting CAS SSO to SonisWeb
    Using phpCAS to pass attributes to script

    By: Jason A. Everling
    Email: jeverling@bshp.edu
-->
<body>

<?php
// Load the settings from config file
require_once 'config.php';

// Load the CAS lib
require_once $phpcas_path . 'CAS.php';

// Enable debugging
//phpCAS::setDebug();

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate
phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// Force CAS authentication
phpCAS::forceAuthentication();

//Get Attribute, Change this to match your Student ID Attribute
$user = phpCAS::getAttribute('studentIDAttribute');

//Database Details
$server = 'SERVER';
$username = 'USERNAME';
$password = 'PASSWORD';
$database = 'DATABASE';

//Test Connection
$con= mssql_connect($server, $username, $password);
if (!$con)
  {
  die('Could not connect: ' . mssql_error());
  }

//Select Database
mssql_select_db($database, $con);

//Build and Execute Query, Adjust pin and soc_sec as needed, default for Sonis
$sql = "SELECT pin FROM name WHERE soc_sec = '$user'";

//Grab Result
list($pinpass) = mssql_fetch_row(mssql_query($sql));

if (!mssql_query($sql,$con))
  {
  die('Error: ' . mssql_error());
  }

//Close Connection
mssql_close($con)

?>

<div id="postForm">
   <form action="https://sonisweburl/studsect.cfm?auth=1" method="post" id="preSSO" >
   <input type="hidden" name="SOC_SEC" value="<?PHP echo $user;?>" />
   <input type="hidden" name="PIN" value="<?PHP echo $pinpass;?>" />
   <input type="submit" style="display:none;"/>
   </form>
</div>
<div id="notice">
   <p style="font-size:2em;" >Please wait while you are signed in......</p>
</div>
</body>
<script type="text/javascript">
    function postSSO () {
        var frm = document.getElementById("preSSO");
        frm.submit();
    }
    window.onload = postSSO;
</script>
</html>
