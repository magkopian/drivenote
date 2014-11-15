<?php require_once '../src/init.php'; 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

// Default pagination limit
define('DEFAULT_LIMIT', 20);

// Check if user is signed in and is admin
if ( !$user->isSignedIn() || !$user->isAdmin() ) {
	header('Location: /');
	die();
}

// Get the page number
$page = 1;
if ( isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] > 0 ) {
	$page = (int) $_GET['page'];
}

$limit = DEFAULT_LIMIT;
$offset = ($page - 1) * $limit;

$users = $user->search(null, $offset, $limit);
$last_page = (int) ceil($users['total_users'] / $limit);

// If current page > last page, redirect to last page
if ( empty($users['records']) && $users['total_users'] > 0 ) {
	header('Location: ?page=' . $last_page);
	die();
}

require '../src/views/pages/users.php';
