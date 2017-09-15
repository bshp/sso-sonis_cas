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

require_once 'config.php';
require_once './cas/CAS.php';

$backurl = 'https://cas.example.com/cas/login?service=https%3A%2F%2Fsonis.example.com%2Fauth%2Flogin.php';

phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

phpCAS::logoutWithRedirectService($backurl);

exit();
?>
</body>
</html>