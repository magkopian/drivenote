<?php

require_once '../includes/init.php';

$googleClient = new Google_Client();
$googleClient->setApplicationName(APP_NAME);

$auth = new GoogleAuth($googleClient);
if ( $auth->isSignedIn() === true ) {
	$auth->signOut();
}

header('Location: signin.php');
die();