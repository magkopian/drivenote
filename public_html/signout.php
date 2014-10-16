<?php require_once '../includes/init.php';

if ( $auth->isSignedIn() === true ) {
	$auth->signOut();
}

header('Location: signin.php');
die();