/******
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

    For Connecting CAS SSO to SonisWeb
    Using phpCAS to pass attributes to script

    By: Jason A. Everling
    Email: jeverling@bshp.edu
******/
CREATE VIEW [dbo].[vw_ssoLogin]
AS
  SELECT dbo.name.soc_sec AS nmid,
    RTRIM(dbo.name.first_name) AS firstname,
    RTRIM(dbo.name.last_name) AS lastname,
    dbo.name.disabled AS nmdisabled,
    RTRIM(dbo.name.ldap_id) AS nmldap,
    RTRIM(dbo.nmmodst.mod_stat) AS modstat,
    RTRIM(CONVERT(char, DECRYPTBYKEYAUTOCERT(CERT_ID('CERTID'), NULL, dbo.name.pin))) AS nmpin,
    CASE WHEN dbo.faculty.soc_sec = dbo.name.soc_sec THEN '1' ELSE '0' END AS multiprof,
    RTRIM(dbo.security.user_id) AS secid, RTRIM(dbo.security.ldap_id) AS secldap,
    RTRIM(CONVERT(char, DECRYPTBYKEYAUTOCERT(CERT_ID('CERTID'), NULL, dbo.security.password))) AS secpin,
    dbo.security.disabled AS secdisabled
  FROM dbo.name INNER JOIN
    dbo.nmmodst ON dbo.name.soc_sec = dbo.nmmodst.soc_sec LEFT OUTER JOIN
    dbo.security ON dbo.name.soc_sec = dbo.security.soc_sec LEFT OUTER JOIN
    dbo.faculty ON dbo.name.soc_sec = dbo.faculty.soc_sec
GO
