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


/**
 * Class ssoUser
 * Functions related to the user(focus)
 */
class ssoUser
{

    /**
     * @param string $sql The sql query to be run, user as lookup value
     * @return mixed Returns the query results.
     */
    public function executePDO($sql)
    {
        global $pdo, $user;
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $user, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results;
    }

    /**
     * @return bool|string
     * Returns false if no $user value, returns $affiliation if found
     * Only call this once as other functions might set a new value.
     */
    public function getFocusAffiliation()
    {
        global $user;
        if (!empty($user)) {
            $sql = "SELECT TOP 1 modstat FROM vw_ssoLogin WHERE nmldap = ? ORDER BY modstat DESC";
            $results = $this->executePDO($sql);
            $modstat = $results['modstat'];
            $affiliation = '';
            if ($modstat == 'FA') {
                $affiliation = 'faculty';
            }
            if ($modstat == 'SF') {
                $affiliation = 'staff';
            }
            if ($modstat == 'ST') {
                $affiliation = 'student';
            }
            return $affiliation;
        }
        return false;
    }


    /**
     * @return array|bool Returns false if not currently affiliated, array otherwise
     * @array array Returns array of attribute value pairs.
     */
    public function getFocusAttributes()
    {
        global $affiliation;
        if ($affiliation == 'student' || $affiliation == 'faculty' || $affiliation == 'staff') {
            $prefix = '';
            if ($affiliation == 'faculty' || $affiliation == 'student') {
                $prefix = 'nm';
            }
            if ($affiliation == 'staff') {
                $prefix = 'sec';
            }
            $sql = "SELECT ".$prefix."id AS soc_sec, ".$prefix."disabled AS disabled, ".$prefix."pin AS pin FROM vw_ssoLogin WHERE ".$prefix."ldap = ?";
            $results = $this->executePDO($sql);
            $status = $results['disabled'];
            $userid = $results['soc_sec'];
            $pinpass = $results['pin'];
            return array('userid' => $userid, 'status' => $status, 'pinpass' => $pinpass);
        }
        return false;
    }


    /**
     * @return bool True or False if user has multiple profiles
     * If user has multiple profiles then True otherwise False
     * To prevent false positives, user must also have FA or SF modstat.
     */
    public function checkMultiProfiles()
    {
        $sql = "SELECT multiprof, modstat FROM vw_ssoLogin WHERE nmldap = ?";
        $results = $this->executePDO($sql);
        $multi = $results['multiprof'];
        $profs = $results['modstat'];
        if ($multi == '1' && $profs == 'FA' || $multi == '1' && $profs == 'SF') {
            return true;
        }
        return false;
    }


    /**
     * @return string Returns a single current level from Sonis,
     * ST will always come first over OA or AL status,
     * Students can apply to other programs during enrollment.
     */
    public function getFocusLevel()
    {
        global $affiliation;
        $level = '';
        if ($affiliation == 'faculty') {
            $level = 'FA';
        }
        if ($affiliation == 'staff') {
            $level = 'ADMN';
        }
        if ($affiliation == 'student') {
            $sql = "SELECT TOP 1 modstat FROM vw_ssoLogin WHERE nmldap = ? ORDER BY modstat DESC";
            $results = $this->executePDO($sql);
            $level = $results['modstat'];
        }
        return $level;
    }
}
