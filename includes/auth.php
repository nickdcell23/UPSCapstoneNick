<?php
require_once dirname(__FILE__) . '/mysql.php';
require_once dirname(__FILE__) . '/oauth.php';

class modAuth {
    var $modDB;
    var $Token;
    var $userData;
    var $userName;
    var $oAuthVerifier;
    var $oAuthChallenge;
    var $oAuthChallengeMethod;
    var $userRoles;
    var $isLoggedIn;
    var $oAuth;

    function __construct($allowAnonymous = '0') {
        $this->modDB = new modDB();
	$this->oAuth = new modOAuth();
	if (session_status() == PHP_SESSION_NONE) session_start();
        $url = _URL . $_SERVER['REQUEST_URI'];

        // check is the user has been logged in already. if they haven't, send them to azure
        if (isset($_SESSION['sessionkey'])) {
            // see if it's still valid. Expiry date doesn't mean that we can't just use the refresh token, so don't test this here
            $res = $this->modDB->QuerySingle('SELECT * FROM tblAuthSessions WHERE txtSessionKey = \'' . $this->modDB->Escape($_SESSION['sessionkey']) . '\'');
            $this->oAuthVerifier = $res['txtCodeVerifier'];
            $this->oAuthChallenge();
            if (!$res || !$res['txtIDToken']) {
                //not in DB or empty id token field
                unset($_SESSION['sessionkey']);
                session_destroy();
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
            
            if (strtotime($res['dtExpires']) < strtotime('+10 minutes')) {
                //attempt token refresh
                if ($res['txtRefreshToken']) {
    	            $oauthRequest = $this->oAuth->generateRequest('grant_type=refresh_token&refresh_token=' . $res['txtRefreshToken'] . '&client_id=' . _OAUTH_CLIENTID . '&scope=' . _OAUTH_SCOPE);
	            $response = $this->oAuth->postRequest('token', $oauthRequest);
                    $reply = json_decode($response);
                    if ($reply->error) {
                        if(substr($reply->error_description, 0, 12) == 'AADSTS70008:') {
                            //refresh token expired
                            $this->modDB->Update('tblAuthSessions', array('txtRedir' => $url, 'txtRefreshToken' => '', 'dtExpires' => date('Y-m-d H:i:s', strtotime('+5 minutes'))),  array('intAuthID' => $res['intAuthID']));
                            $oAuthURL = 'https://login.microsoftonline.com/' . _OAUTH_TENANTID . '/oauth2/v2.0/' . 'authorize?response_type=code&client_id=' . _OAUTH_CLIENTID . '&redirect_uri=' . urlencode(_URL . '/oauth.php') . '&scope=' . _OAUTH_SCOPE . '&code_challenge=' . $this->oAuthChallenge . '&code_challenge_method=' . $this->oAuthChallengeMethod;
                            header('Location: ' . $oAuthURL);
                            exit;
                        }
			echo $this->oAuth->errorMessage($reply->error_description);
			exit;
                    }
		    $idToken = base64_decode(explode('.', $reply->id_token)[1]);
		    $this->modDB->Update('tblAuthSessions', array('txtToken' => $reply->access_token, 'txtRefreshToken' => $reply->refresh_token, 'txtIDToken' => $idToken, 'txtRedir' => '', 'dtExpires' => date('Y-m-d H:i:s', strtotime('+' . $reply->expires_in . ' seconds'))), array('intAuthID' => $res['intAuthID']));
    		    $res['txtToken'] = $reply->access_token;
                }
            }
            //Populate userData and userName in database
            $this->Token = $res['txtToken'];
	    if ($res['txtIDToken']) {
		$idToken = json_decode($res['txtIDToken']);
		$this->userName = $idToken->preferred_username;
		if (isset($idToken->roles)) {
			$this->userRoles = $idToken->roles;
		} else {
			$this->userRoles = array('Default Access');
		}
	    }
	    $this->isLoggedIn = 1;
        } else {
	    if (!$allowAnonymous || $_GET['login'] == '1') {
                // Generate the code verifier and challenge
                $this->oAuthChallenge();
                // Generate a session key and store in cookie, then populate database
                $sessionKey = $this->uuid();
                $_SESSION['sessionkey'] = $sessionKey;
                $this->modDB->Insert('tblAuthSessions', array('txtSessionKey' => $sessionKey, 'txtRedir' => $url, 'txtCodeVerifier' => $this->oAuthVerifier, 'dtExpires' => date('Y-m-d H:i:s', strtotime('+5 minutes'))));
                // Redirect to Azure AD login page
                $oAuthURL = 'https://login.microsoftonline.com/' . _OAUTH_TENANTID . '/oauth2/v2.0/' . 'authorize?response_type=code&client_id=' . _OAUTH_CLIENTID . '&redirect_uri=' . urlencode(_URL . '/oauth.php') . '&scope=' . _OAUTH_SCOPE . '&code_challenge=' . $this->oAuthChallenge . '&code_challenge_method=' . $this->oAuthChallengeMethod;
                header('Location: ' . $oAuthURL);
                exit;
	    }
        }
        //Clean up old entries
	// Refresh tokens are deleted after 72 hours, always
	$maxRefresh = strtotime('-72 hour');
        $this->modDB->Query('DELETE FROM tblAuthSessions WHERE dtExpires < \'' . date('Y-m-d H:i:s', $maxRefresh) . '\'');
    }

    function checkUserRole($role) {
	// Check that the requested role has been assigned to the user
	if (in_array($role, $this->userRoles)) {
	    return 1;
	}
	return;
    }

    function uuid() {
        // This isn't mine, but I couldn't find credits to whoever made it
        //uuid version 4
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    function oAuthChallenge() {
        // Function to generate code verifier and code challenge for oAuth login. Check RFC7636 oAuth documentation
        $verifier = $this->oAuthVerifier;
        if (!$this->oAuthVerifier) {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._~';
            $charLen = strlen($chars) - 1;
            $verifier = '';
            for ($i = 0; $i < 128; $i++) {
                $verifier .= $chars[mt_rand(0, $charLen)];
            }
            $this->oAuthVerifier = $verifier;
        }
        $this->oAuthChallenge = str_replace('=', '', strtr(base64_encode(pack('H*', hash('sha256', $verifier))), '+/', '-_'));
        $this->oAuthChallengeMethod = 'S256';
    }
}
?>
