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
include 'lib.php';

// Enable debugging
//phpCAS::setDebug();

session_set_cookie_params($client_lifetime, $client_path, $client_domain, $client_secure, $client_httpOnly);
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate
//phpCAS::setCasServerCACert(./CACert.pem);
phpCAS::setNoCasServerValidation();

if (!phpCAS::isAuthenticated()) {
    phpCAS::forceAuthentication();
}

$user = phpCAS::getUser();
$affiliation = phpCAS::getAttribute('eduPersonAffiliation');
$bshpid = phpCAS::getAttribute('ATTRIBUTE');
$focus = new ssoUser();

?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<?php
if(isset($_POST['submit'])) {
    if ($_POST['submit'] == 'Faculty') {
        $modchoice = 'FA';
    }
    if ($_POST['submit'] == 'Staff') {
        $modchoice = 'ADMN';
    }
//set preferred login for multi-role users
    $prefmodule = $focus->getFocusChosenAttributes();
    ?>
    <div id="postForm">
        <form action="../cas_login_chk.cfm" method="post" id="postSSOForm" name="postSSOForm">
            <input type="hidden" name="modstat" value="<?PHP echo $modchoice; ?>"/>
            <input type="hidden" name="PID" value="<?PHP echo $prefmodule['userid']; ?>"/>
            <input type="hidden" name="PIN" value="<?PHP echo $prefmodule['pinpass']; ?>"/>
            <input type="submit"/>
        </form>
    </div>
    <div id="notice">
        <p>Please wait while you are signed in......</p>
    </div>
    <script type="text/javascript">
        document.postSSOForm.submit();
    </script>
<?php
}
//Get user values
$attributes = $focus->getFocusAttributes();
$profiles = $focus->getFocusProfiles();
$module = $focus->getFocusModStat();
$auth = array('FA', 'ADMN', 'ST');

if (!isset($_POST['submitted'])) {
    if (!in_array($module, $auth)) {
    ?>
        <div id="notice">
            <p>Your account does not yet include Sonis access. Students, contact the registrar's office. Faculty or Staff, contact the IS Department</p>
        </div>
    <?php
    } else if ($attributes['status'] == '0' && $attributes && $profiles == false) {
        ?>
        <div id="postForm">
            <form action="../cas_login_chk.cfm" method="post" id="preSSO" name="preSSOForm">
                <input type="hidden" name="modstat" value="<?PHP echo $module; ?>"/>
                <input type="hidden" name="PID" value="<?PHP echo $attributes['userid']; ?>"/>
                <input type="hidden" name="PIN" value="<?PHP echo $attributes['pinpass']; ?>"/>
                <input type="submit"/>
            </form>
        </div>
        <div id="notice">
            <p>Please wait while you are signed in......</p>
        </div>
        <script type="text/javascript">
            document.preSSOForm.submit();
        </script>
    <?php
    } else if ($attributes['status'] == '1') {
    ?>
        <div id="notice">
            <p>Your account has been locked for your own security, please <a href="https://support.bshp.edu">submit a ticket to have it unlocked.</a></p>
        </div>
    <?php
    } else if ($profiles == true) {
    ?>
        <div id="choices">
            <div id="multiprofile">
                <p>You have multiple profiles,<br>Select the profile you wish to login as.</p>
            </div>
            <div id="choicePostForm">
                <form action="" method="post" id="profileForm" name="profileForm">
                    <input type="submit" name="submit" value="Faculty">
                    <input type="submit" name="submit" value="Staff">
                </form>
            </div>
        </div>
    <?php
    } else {
    ?>
        <div id="notice">
            <p>Unable to determine your status, please <a href="https://support.bshp.edu">submit a ticket</a></p>
        </div>
    <?php
    }
}
?>
</body>
</html>
