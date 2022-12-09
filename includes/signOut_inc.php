<?php
include './auth.php';

session_start();
if ($_GET['action'] == 'logout') {
    // Logout action selected, clear session, log them out
    $this->modDB->Delete('tblAuthSessions', array('intAuthID' => $res['intAuthID']));
    session_unset($_SESSION['sessionkey'], $SESSION['email']);
    session_destroy();
    header('Location: ' . _OAUTH_LOGOUT);
    exit;
}

session_unset();
session_destroy();
header('location: ../login.php');
exit();