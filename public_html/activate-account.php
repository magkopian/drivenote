<?php require_once '../src/init.php';

if ( !$auth->isSignedIn() ) {
	header('Location: /');
	die();
}
else if ( !isset($_POST['academic_email']) || empty($_POST['academic_email']) ) {
	Notifier::push('warning', 'Please fill your academic email address.');
	header('Location: /');
	die();
}

$academic_email = mb_strtolower(trim($_POST['academic_email']), 'UTF-8');

$regex = '/^cse[0-9]{5}@stef\.teipir\.gr$/';

// If the Academic Email is valid send activation email and print success
if ( preg_match($regex, $academic_email) ) {
	
	if ( $auth->academicEmailExists($academic_email) ) {
		Notifier::push('warning', 'This academic email address is already in use.');
		header('Location: /');
		die();
	}
	
	echo $message = 'Click to the link below to verify your account ' . $auth->getVerifyURL($academic_email);
	
	//$mailer = new Mailer();
	//$mailer->send($academic_email, 'Drivenote | Account Verification', $message);
	
} // Else if the Academic Email is invalid print failure
else {
	Notifier::push('error', 'Invalid academic email address.');
	header('Location: /');
	die();
}