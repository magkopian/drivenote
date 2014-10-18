<?php require_once '../src/init.php';

if ( $user->isSignedIn() === true ) {
	$auth->signOut();
}

header('Location: /');
die();