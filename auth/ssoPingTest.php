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

require_once 'config.php';
require_once './cas/CAS.php';

// Enable debugging
//phpCAS::setDebug();

session_set_cookie_params($client_lifetime, $client_path, $client_domain, $client_secure, $client_httpOnly);
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate
//phpCAS::setCasServerCACert(./CACert.pem);
phpCAS::setNoCasServerValidation();
phpCAS::forceAuthentication();

//Get Attribute, Change this to match your Student ID Attribute
$user = phpCAS::getUser();
$affiliation = phpCAS::getAttribute('eduPersonAffiliation');

//Build and Execute Query, Adjust pin and soc_sec as needed, default for Sonis
if ($affiliation == 'student' || $affiliation == 'faculty' ) {
    $stmt = $pdo->prepare("SELECT soc_sec, disabled, pin FROM name WHERE ldap_id = ?");
    $stmt->bindParam(1, $user, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $userid = $row['soc_sec'];
    $status = $row['disabled'];
    $pinpass = $row['pin'];
}
else if ($affiliation == 'staff') {
    $stmt = $pdo->prepare("SELECT user_id, disabled, password FROM security WHERE ldap_id = ?");
    $stmt->bindParam(1,$user, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $userid = $row['user_id'];
    $status = $row['disabled'];
    $pinpass = $row['password'];
}
else {
    exit("Unable to determine affiliated status, please contact tech support");
}
if ($affiliation == 'faculty') {
    $modstat = 'FA';
}
if ($affiliation == 'staff') {
    $modstat = 'ADMN';
}
if ($affiliation == 'student') {
    $modstat = 'ST';
}
if ($status == '0') {
?>
<html>
<body>
    <div id="postForm">
        <form action="../cas_login_chk.cfm" method="post" id="preSSO" name="preSSOForm">
            <input type="hidden" name="modstat" value="<?PHP echo $modstat; ?>"/>
            <input type="hidden" name="PID" value="<?PHP echo $userid; ?>"/>
            <input type="hidden" name="PIN" value="<?PHP echo $pinpass; ?>"/>
            <input type="submit" style="display:none;"/>
        </form>
    </div>
    <div id="notice">
        <p style="font-size:1.5em;">Please wait while you are signed in......</p>
    </div>
    <script type="text/javascript">
        document.preSSOForm.submit();
    </script>
<?php
} else {
?>
    <div id="notice">
        <p style="font-size:1.5em;">Your Sonis account is disabled, please <a href="https://support.bshp.edu">submit a ticket</a></p>
    </div>
<?php
}
?>
</body>
</html>