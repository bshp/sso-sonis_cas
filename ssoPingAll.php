<html>
<body>
<?php
//
//   Copyright 2015 Jason A. Everling
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
//
//
//    For Connecting CAS SSO to SonisWeb
//    Using phpCAS to pass attributes to script
//
//    By: Jason A. Everling
//    Email: jeverling@bshp.edu
//

// Load the settings from config file
require_once 'config.all.php';

// Load the CAS lib
require_once './cas/CAS.php';

// Enable debugging
//phpCAS::setDebug();

//phpCAS Cookie
session_set_cookie_params($client_lifetime, $client_path, $client_domain, $client_secure, $client_httpOnly);

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate
//phpCAS::setCasServerCACert(./CACert.pem);
phpCAS::setNoCasServerValidation();

// force CAS authentication
phpCAS::forceAuthentication();

//Get Attribute, Change this to match your Student ID Attribute
$user = phpCAS::getUser();
$affiliation = phpCAS::getAttribute('eduPersonAffiliation');

//Build and Execute Query, Adjust pin and soc_sec as needed, default for Sonis

if ($affiliation == 'student' || $affiliation == 'faculty' ) {
    $stmt = $pdo->prepare("SELECT soc_sec,pin FROM name WHERE ldap_id = ?");
    $stmt->bindParam(1,$user, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $userid = $row['soc_sec'];
    $pinpass = $row['pin'];
}
else if ($affiliation == 'staff') {
    $stmt = $pdo->prepare("SELECT user_id,password FROM security WHERE ldap_id = ?");
    $stmt->bindParam(1,$user, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $userid = $row['user_id'];
    $pinpass = $row['password'];
}
else {
    echo "No Access";
}
?>
<div id="postForm">
    <form action="../../cas_login_chk.cfm" method="post" id="preSSO">
<?php
if ($affiliation == 'student') {
    ?>
            <input type="hidden" name ="modstat" value="ST"/>
<?php
} else if ($affiliation == 'faculty') {
    ?>
            <input type="hidden" name ="modstat" value="FA"/>

<?php
} else if ($affiliation == 'staff') {
    ?>
            <input type="hidden" name ="modstat" value="ADMN"/>
<?php
} else {
    ?>
        <p>No Access Rights Found, Please Contact Tech Support</p>
<?php
}
?>
            <input type="hidden" name="PID" value="<?PHP echo $userid;?>" />
            <input type="hidden" name="PIN" value="<?PHP echo $pinpass;?>" />
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