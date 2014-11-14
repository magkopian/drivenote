<?php require_once '../src/init.php'; 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

// Check if user is signed in and is admin
if ( !$user->isSignedIn() || !$user->isAdmin() ) {
	header('Location: /');
	die();
}

// TODO: PAGINATION

$users = $user->search(null, 0, 9999);

require '../src/views/pages/admin.php';
