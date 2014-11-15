<?php require_once '../src/init.php'; 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

// Default pagination limit
$valid_limits = array (25, 50, 75, 100);

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

// Get users per page
$limit = $valid_limits[1];
if ( isset($_POST['limit']) && !empty($_POST['limit']) && in_array($_POST['limit'], $valid_limits) ) {
	$limit = (int) $_POST['limit'];
	$_SESSION['users_pagination_limit'] = $limit;
}
else if ( isset($_SESSION['users_pagination_limit']) && !empty($_SESSION['users_pagination_limit']) && in_array($_SESSION['users_pagination_limit'], $valid_limits) ) {
	$limit = $_SESSION['users_pagination_limit'];
}

// Calculate the offset
$offset = ($page - 1) * $limit;

// Get the users
$users = $user->search(null, $offset, $limit);

// Find the last page
$last_page = (int) ceil($users['total_users'] / $limit);

// If current page > last page, redirect to last page
if ( empty($users['records']) && $users['total_users'] > 0 ) {
	header('Location: ?page=' . $last_page);
	die();
}

require '../src/views/pages/users.php';
