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

//Functions related to the focus account
class ssoUser
{
    //Check the users affiliation, if valid, return the attributes,
    //if invalid, return false
    public function getFocusAttributes()
    {
        global $pdo, $user, $affiliation, $bshpid;
        if ($affiliation == 'student' || $affiliation == 'faculty' || $affiliation == 'staff') {
            if ($affiliation == 'faculty') {
                $stmt = $pdo->prepare("SELECT name.soc_sec, name.disabled, name.pin, nmmodst.mod_stat FROM name INNER JOIN nmmodst ON name.soc_sec = nmmodst.soc_sec WHERE name.ldap_id = ?");
            }
            if ($affiliation == 'staff') {
                $stmt = $pdo->prepare("SELECT user_id, disabled, password FROM security WHERE ldap_id = ?");
            }
            if ($affiliation == 'student') {
                $stmt = $pdo->prepare("SELECT name.soc_sec, name.disabled, name.pin, nmmodst.mod_stat FROM name INNER JOIN nmmodst ON name.soc_sec = nmmodst.soc_sec WHERE name.soc_sec = ?");
                $user = $bshpid;
            }
            $stmt->bindParam(1, $user, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = $row['disabled'];
            if ($affiliation == 'student' || $affiliation == 'faculty') {
                $userid = $row['soc_sec'];
                $pinpass = $row['pin'];
                $level = $row['level'];
            }
            if ($affiliation == 'staff') {
                $userid = $row['user_id'];
                $pinpass = $row['password'];
                $level = 'SF';
            }
            return array('userid' => $userid, 'status' => $status, 'pinpass' => $pinpass, 'level' => $level);
        }
        return false;
    }

    //Check if user has multiple profiles, return true or false
    public function getFocusProfiles()
    {
        global $pdo, $user;
        $stmt = $pdo->prepare("SELECT name.ldap_id, security.ldap_id AS sec_id, faculty.soc_sec FROM name INNER JOIN security ON name.soc_sec = security.soc_sec INNER JOIN faculty ON name.soc_sec = faculty.soc_sec WHERE name.ldap_id = ?");
        $stmt->bindParam(1, $user, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($results['ldap_id'] == $results['sec_id'] && $results) {
            return true;
        }
        return false;
    }

    //If the user has multiple profiles, return the values for the choice
    //submitted by the user
    public function getFocusChosenAttributes()
    {
        global $pdo, $user, $modchoice;
        if ($modchoice == 'FA' || $modchoice == 'ADMN') {
            if ($modchoice == 'FA') {
                $stmt = $pdo->prepare("SELECT soc_sec, disabled, pin FROM name WHERE ldap_id = ?");
            }
            if ($modchoice == 'ADMN') {
                $stmt = $pdo->prepare("SELECT user_id, disabled, password FROM security WHERE ldap_id = ?");
            }
            $stmt->bindParam(1, $user, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = $row['disabled'];
            if ($modchoice == 'FA') {
                $userid = $row['soc_sec'];
                $pinpass = $row['pin'];
            }
            if ($modchoice == 'ADMN') {
                $userid = $row['user_id'];
                $pinpass = $row['password'];
            }
            return array('userid' => $userid, 'status' => $status, 'pinpass' => $pinpass);
        }
        return false;
    }

    //Set modstat for Sonis sign in form
    public function getFocusModStat()
    {
        global $affiliation;
        if ($affiliation == 'faculty') {
            $modstat = 'FA';
        }
        if ($affiliation == 'staff') {
            $modstat = 'ADMN';
        }
        if ($affiliation == 'student') {
            $modstat = 'ST';
        }
        if ($affiliation == '') {
            $modstat = 'mod_error';
        }
        return $modstat;
    }
}