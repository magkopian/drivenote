<?php require_once '../src/init.php';
/************************************************\
 * Copyright (c) 2016 Manolis Agkopian          *
 * See the file LICENCE for copying permission. *
\************************************************/

// Check if user is signed in and is admin
if ( !$user->isSignedIn() || !$user->isAdmin() ) {
	header('Location: /');
	die();
}

// Fetch the users that are verified but not yet approved
$where = [
	[
		'field'    => 'verified',
		'operator' => '=',
		'value'    => '1',
		'restrict' => true
	],
	[
		'field'    => 'approved',
		'operator' => '=',
		'value'    => '0',
		'restrict' => true
	]
];

// Get the users
$users = $user->search($where, 0, 10);

require '../src/views/pages/members.php';
