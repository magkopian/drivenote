<?php require_once '../src/init.php';

if ( !$user->isSignedIn() || $user->isVerified() ) {
	header('Location: /');
	die();
}
else if ( !isset($_POST['academic_email']) || empty($_POST['academic_email']) ) {
	Notifier::push('warning', 'Please fill your academic email address.');
	header('Location: /');
	die();
}
else {
	$academic_email = mb_strtolower(trim($_POST['academic_email']), 'UTF-8');
	$regex = '/^cse[0-9]{5}@stef\.teipir\.gr$/';
	
	// Check if the Academic Email is valid
	if ( !preg_match($regex, $academic_email) ) {
		Notifier::push('error', 'Please give a valid academic email address.');
		header('Location: /');
		die();
	}
}
	
try {
	
	$verifier = new Verifier($user, $db);
	
	//Check if already verified user has the same email
	if ( $verifier->isEmailVerified($academic_email) ) {
		Notifier::push('warning', 'This academic email is already in use.');
		header('Location: /');
		die();
	}
	
	$user->setAcademicEmail($academic_email);
	$verifyLink = $verifier->getVerifyURL();
	$user->save();
	
	$message = 'Click to the link below to verify your account ' . $verifyLink;
	
	$mailer = Mailer::getInstance();
	$mailer->send($academic_email, 'Drivenote | Account Verification', $message);
	
	Notifier::push('success', 'A verification email has been sent to your academic email. Please check your inbox.');
	header('Location: /');
	die();
	
}
catch ( Exception $e ) {
	Notifier::push('error', 'Couldn\'t send verification email. Please contact the administrator.' . $e->getMessage());
	header('Location: /');
	die();
}
