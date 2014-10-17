<?php require_once '../src/init.php';

if ( !isset($_GET['code']) || empty($_GET['code']) ) {
	header('Location: /');
	die();
}

if ( $auth->isSignedIn() === false ) {

	try {
		if ( $auth->signIn($_GET['code']) === false ) {
			Notifier::push('warning', 'The session has expired please sign in again.');
		}
	}
	catch ( Exception $e ) { // Print Error Messsage
		Notifier::push('error', 'Unable to sign in. Please contact the administrator.');
	}
	
}

header('Location: /');
die();