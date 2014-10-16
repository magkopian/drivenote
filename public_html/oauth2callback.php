<?php

require_once '../includes/init.php';

if ( !isset($_GET['code']) || empty($_GET['code']) ) {
	header('Location: signin.php');
	die();
}

$googleClient = new Google_Client();
$googleClient->setApplicationName(APP_NAME);

$auth = new GoogleAuth($googleClient);

if ( $auth->isSignedIn() === false ) {
	
	try {
		$auth->signIn($_GET['code']);
	}
	catch ( Exception $e ) {
		// Print Error Messsage
	}
	
}

header('Location: signin.php');
die();