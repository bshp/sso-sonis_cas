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
    //Run query
    public function executePDO($sql)
    {
        global $pdo, $user;
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $user, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results;
    }

    //Check the users affiliation, if valid, return the attributes,
    //if invalid, return false
    public function getFocusAttributes()
    {
        global $affiliation;
        if ($affiliation == 'student' || $affiliation == 'faculty' || $affiliation == 'staff') {
            if ($affiliation == 'faculty' || $affiliation == 'student') {
                $prefix = 'nm';
            }
            if ($affiliation == 'staff') {
                $prefix = 'sec';
            }
            $sql = "SELECT $prefix.id AS soc_sec, $prefix.disabled AS disabled, $prefix.pin AS pin FROM vw_ssoLogin WHERE $prefix.ldap = ?";
            $results = $this->executePDO($sql);
            $status = $results['disabled'];
            $userid = $results['soc_sec'];
            $pinpass = $results['pin'];
            return array('userid' => $userid, 'status' => $status, 'pinpass' => $pinpass);
        }
        return false;
    }

    //Check if user has multiple profiles, return true or false
    public function checkMultiProfiles()
    {
        $sql = "SELECT name.ldap_id, security.ldap_id AS sec_id, faculty.soc_sec FROM name INNER JOIN security ON name.soc_sec = security.soc_sec INNER JOIN faculty ON name.soc_sec = faculty.soc_sec WHERE name.ldap_id = ?";
        $results = $this->executePDO($sql);
        if ($results['ldap_id'] == $results['sec_id'] && $results) {
            return true;
        }
        return false;
    }

    //Get current level, static for fac/staff, students are checked.
    //Function getModStat is basically a duplicate and this could replace it.
    public function getFocusLevel()
    {
        global $affiliation;
        if ($affiliation == 'faculty') {
            $level = 'FA';
        }
        if ($affiliation == 'staff') {
            $level = 'ADMN';
        }
        if ($affiliation == 'student') {
            $sql = "SELECT modstat FROM vw_ssoLogin WHERE nmldap = ? ORDER BY modstat DESC";
            $results = $this->executePDO($sql);
            $level = $results['modstat'];
        }
        return $level;
    }

    //If the user has multiple profiles, return the values for the choice
    //submitted by the user
    public function getFocusChosenAttributes()
    {
        global $modchoice;
        if ($modchoice == 'FA' || $modchoice == 'ADMN') {
            if ($modchoice == 'FA') {
                $prefix = 'nm';
            }
            if ($modchoice == 'ADMN') {
                $prefix = 'sec';
            }
            $sql = "SELECT $prefix.id AS soc_sec, $prefix.disabled AS disabled, $prefix.pin AS pin FROM vw_ssoLogin WHERE $prefix.ldap = ?";
            $results = $this->executePDO($sql);
            $status = $results['disabled'];
            $userid = $results['soc_sec'];
            $pinpass = $results['pin'];
            return array('userid' => $userid, 'status' => $status, 'pinpass' => $pinpass);
        }
        return false;
    }
}
