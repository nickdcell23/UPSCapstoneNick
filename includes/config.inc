<?php

// MySQL connection details
define('_MYSQL_HOST', 'localhost');
define('_MYSQL_USER', 'root');
define('_MYSQL_DB', 'ups');
define('_MYSQL_PASS', '');

define('_OAUTH_TENANTID', '9c186597-41ca-4df0-9f9a-efca0fd342d7');
define('_OAUTH_CLIENTID', '7581f467-b21b-4c6a-9760-c39248102784');
define('_OAUTH_LOGOUT', 'http://localhost/UPSCapstoneFall2022-main/login.php');
define('_OAUTH_SCOPE', 'openid%20offline_access%20profile%20user.read');

// Either we use a secret key or a certificate
// Going with secret key, since it's easier

define('_OAUTH_METHOD', 'secret');

define('_OAUTH_SECRET', 'vDe8Q~AoBhI5kezdx143UgWU3jFUwkPt~_Zkkami');
// not using these, so ignore them. They're here if you wish to change your mind though
define('_OAUTH_AUTH_CERTFILE', '/path/to/certificate.crt');
define('_OAUTH_AUTH_KEYFILE', '/path/to/privatekey.pem');

// URL to this website, no trailing slash.
define('_URL', 'http://localhost/UPSCapstoneFall2022-main');
?>

