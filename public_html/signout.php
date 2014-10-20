<?php require_once '../src/init.php';
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

if ( $user->isSignedIn() === true ) {
	$auth->signOut();
}

header('Location: /');
die();