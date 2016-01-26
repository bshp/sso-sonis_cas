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
require_once '../config.php';

// Load the CAS lib
require_once '../cas/CAS.php';

// Enable debugging
//phpCAS::setDebug();

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate
//phpCAS::setCasServerCACert(../CACert.pem);
phpCAS::setNoCasServerValidation();

// Force CAS authentication
phpCAS::forceAuthentication();

//Get Attribute, Change this to match your Student ID Attribute
$user = phpCAS::getUser();

//Build and Execute Query, Adjust pin and soc_sec as needed, default for Sonis
$query = mssql_query("SELECT soc_sec,pin FROM name WHERE ldap_id = '$user'");

//Grab Result
$row = mssql_fetch_row($query);

//Close Connection
mssql_close($con)

?>

<div id="postForm">
   <form action="<?PHP echo $facultyURL;?>" method="post" id="preSSO" >
   <input type="hidden" name="SOC_SEC" value="<?PHP echo $row[0];?>" />
   <input type="hidden" name="PIN" value="<?PHP echo $row[1];?>" />
   <input type="submit" style="display:none;"/>
   </form>
</div>
<div id="notice">
   <p style="font-size:1.5em;" >Please wait while you are signed in......</p>
</div>
</body>
<script type="text/javascript">
    function postSSOForm () {
        var frm = document.getElementById("preSSO");
        frm.submit();
    }
    window.onload = postSSOForm;
</script>
</html>
