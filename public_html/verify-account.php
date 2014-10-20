<?php require_once '../src/init.php';
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

if ( $user->isVerified() ) {
	header('Location: /');
	die();
}
else if ( !isset($_GET['uid']) || empty($_GET['uid']) || !isset($_GET['token']) || empty($_GET['token']) ) {
	Notifier::push('warning', 'The verification link has expired. Please send the verification email again.');
	header('Location: /');
	die();
}
else if ( !$user->isSignedIn() || $user->getUserId() != (int) $_GET['uid'] ) {
	Notifier::push('warning', 'You need to sign in before trying to verify your academic email.');
	header('Location: /');
	die();
}

$user_id = (int) $_GET['uid'];
$token = trim($_GET['token']);

try {
	
	$verifier = new Verifier($user, $db);
	
	if ( $verifier->isEmailVerified($user->getAcademicEmail()) ) {
		Notifier::push('warning', 'Cannot verify account, the academic email is already in use.');
		header('Location: /');
		die();
	} // Check if verification link has expired
	else if ( $verifier->isTokenExpired($user_id, $token) ) {
		Notifier::push('warning', 'The verification link has expired. Please send the verification email again.');
		header('Location: /');
		die();
	} // If everything ok verify the account
	else {
		$verifier->verify($user_id, $token);

		$drive->grantReadAccess($user->getGoogleEmail(), DIRECTORY_ID);
		
		Notifier::push('success', 'Your account has been verified!');
		header('Location: /');
		die();
	}
	
}
catch ( Exception $e ) {
	$logger = new ExceptionLogger();
	$logger->error($e);
	Notifier::push('error', 'Your account could not be verified. Please contact the administrator.' . $e->getMessage());
	header('Location: /');
	die();
}