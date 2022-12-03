<?php
/* config.inc Configuration for oAuth Demo project
 *
 * Katy Nicholson, last updated 17/11/2021
 *
 * https://github.com/CoasterKaty
 * https://katytech.blog/
 * https://twitter.com/coaster_katy
 *
 

***RENAME TO config.inc ***

*/
// MySQL connection details
define('_MYSQL_HOST', 'localhost');
define('_MYSQL_USER', 'root');
define('_MYSQL_DB', 'ups');
define('_MYSQL_PASS', '');


// oAuth details



// TENANTID: Your tenant's ID if you set up the app reg as single tenant, otherwise 'common'

//Scope needs to include the services you wish to access - at a minimum you'll need openid and offline_access for logging on to work. Add in user.read if you want to query user profile via Graph. Separate with %20.

define('_OAUTH_TENANTID', '9c186597-41ca-4df0-9f9a-efca0fd342d7');
define('_OAUTH_CLIENTID', '7581f467-b21b-4c6a-9760-c39248102784');
define('_OAUTH_LOGOUT', 'https://login.microsoftonline.com/common/wsfederation?wa=wsignout1.0');
define('_OAUTH_SCOPE', 'openid%20offline_access%20profile%20user.read');

// Define either the client secret, or the client certificate details
// method = 'certificate' or 'secret'

define('_OAUTH_METHOD', 'secret');

define('_OAUTH_SECRET', 'vDe8Q~AoBhI5kezdx143UgWU3jFUwkPt~_Zkkami');
define('_OAUTH_AUTH_CERTFILE', '/path/to/certificate.crt');
define('_OAUTH_AUTH_KEYFILE', '/path/to/privatekey.pem');

// on Windows, the certificate paths should be in the form c:/path/to/cert.crt


// URL to this website, no trailing slash.
define('_URL', 'http://localhost/UPSCapstoneFall2022-main');
?>

