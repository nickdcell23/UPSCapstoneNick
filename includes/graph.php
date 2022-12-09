<?php
require_once dirname(__FILE__) . '/auth.php';

// Code gets the user's profile. This way, we know who signed into Azure inside the app itself

class modGraph {
        var $modAuth;
        function __construct() {
                $this->modAuth = new modAuth();
        }
        function getProfile() {
                $profile = json_decode($this->sendGetRequest('https://graph.microsoft.com/v1.0/me/'));
                return $profile;
        }
        function getPhoto() {
                $photoType = json_decode($this->sendGetRequest('https://graph.microsoft.com/v1.0/me/photo/'));
                $photo = $this->sendGetRequest('https://graph.microsoft.com/v1.0/me/photo/%24value');
                if (isset($photoType->{'@odata.mediaContentType'})) {
                        return '<img src="data:' . $photoType->{'@odata.mediaContentType'} . ';base64,' . base64_encode($photo) . '" alt="User Photo" />';
                }
                return;
        }

        function sendGetRequest($URL, $ContentType = 'application/json') {
                $ch = curl_init($URL);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->modAuth->Token, 'Content-Type: ' . $ContentType));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);

                curl_close($ch);
                return $response;
        }
}
?>
