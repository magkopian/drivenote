<?php require_once '../src/init.php';

if ( !isset($_GET['uid']) || empty($_GET['uid']) || !isset($_GET['token']) || empty($_GET['token']) ) {
	Notifier::push('warning', 'The verification link has expired. Please resend a verification email.');
	header('Location: /');
	die();
}

$user_id = (int) $_GET['uid'];
$token = trim($_GET['token']);

try {
	
	// TODO: Check if email already verified by another user
	
	if ( $auth->isVerifyTokenExpired($user_id, $token) ) {
		Notifier::push('warning', 'The verification link has expired. Please resend a verification email.');
		header('Location: /');
		die();
	}
	else {
		$auth->verify($user_id, $token);
		Notifier::push('success', 'Your account has been verified!');
		header('Location: /');
		die();
	}
	
}
catch ( Exception $e ) {
	Notifier::push('Error', 'Your account could not be verified. Please contact the administrator.');
	header('Location: /');
	die();
}