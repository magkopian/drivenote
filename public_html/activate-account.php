<?php require_once '../src/init.php';

if ( !$auth->isSignedIn() || $auth->isVerified() ) {
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
	
	try {
		
		//Check if already verified user has the same email
		if ( $auth->isVerifiedEmailExists($academic_email) ) {
			Notifier::push('warning', 'This academic email is already in use.');
			header('Location: /');
			die();
		}
		
		echo $message = 'Click to the link below to verify your account ' . $auth->getVerifyURL($academic_email);
		die();
		
		$mailer = new Mailer();
		$mailer->send($academic_email, 'Drivenote | Account Verification', $message);
		
		Notifier::push('success', 'A verification email has been sent to your academic email. Please check your inbox.');
		header('Location: /');
		die();
		
	}
	catch ( Exception $e ) {
		Notifier::push('error', 'Couldn\'t send verification email. Please contact the administrator.');
		header('Location: /');
		die();
	}
	
} // Else if the Academic Email is invalid print failure
else {
	Notifier::push('error', 'Please give a valid academic email address.');
	header('Location: /');
	die();
}