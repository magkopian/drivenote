<?php require_once '../src/init.php';

if ( !isset($_GET['code']) || empty($_GET['code']) || $auth->isSignedIn() ) {
	header('Location: /');
	die();
}

try {
	if ( $auth->signIn($_GET['code']) === false ) {
		Notifier::push('warning', 'The session has expired please sign in again.');
	}
	else {
		Notifier::push('success', 'You have successfully signed in!');
	}
}
catch ( Exception $e ) {
	Notifier::push('error', 'Unable to sign in. Please contact the administrator.');
}

header('Location: /');
die();