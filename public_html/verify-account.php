<?php require_once '../src/init.php';

if ( !isset($_GET['uid']) || empty($_GET['uid']) || !isset($_GET['token']) || empty($_GET['token']) ) {
	Notifier::push('warning', 'The verification link has expired. Please send the verification email again.');
	header('Location: /');
	die();
}

$user_id = (int) $_GET['uid'];
$token = trim($_GET['token']);

try {
	
	$verifier = new Verifier($user, $db);
	$academic_email = $verifier->getAcademicEmailByUserId($user_id);
	
	if ( $verifier->isEmailVerified($academic_email) ) {
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
		Notifier::push('success', 'Your account has been verified!');
		header('Location: /');
		die();
	}
	
}
catch ( Exception $e ) {
	Notifier::push('error', 'Your account could not be verified. Please contact the administrator.');
	header('Location: /');
	die();
}