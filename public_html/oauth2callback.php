<?php require_once '../includes/init.php';

if ( !isset($_GET['code']) || empty($_GET['code']) ) {
	header('Location: signin.php');
	die();
}

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