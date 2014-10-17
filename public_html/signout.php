<?php require_once '../src/init.php';

if ( $auth->isSignedIn() === true ) {
	$auth->signOut();
}

header('Location: /');
die();