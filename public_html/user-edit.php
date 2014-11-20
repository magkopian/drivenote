<?php require_once '../src/init.php';
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

if ( !isset($_POST['action'], $_POST['users']) || empty($_POST['action']) || empty($_POST['users']) || 
		!is_array($_POST['users']) || !in_array($_POST['action'], array('delete', 'revoke-read', 'grant-read')) ) {
	header('HTTP/1.1 404 Not Found');
	die();
}

$users = array();
foreach ( $_POST['users'] as $user_id ) {
	if ( ($users[] = (int) $user_id) <= 0 ) {
		header('HTTP/1.1 404 Not Found');
		die();
	}
}

try {
	if ( $_POST['action'] == 'delete' ) {
		$user->delete($users);
	}
	else if ( $_POST['action'] == 'revoke-read' ) {
		// Revoke read access
	}
	else if ( $_POST['action'] == 'grant-read' ) {
		// Grant read access
	}
	
	$responce = array(
		'status' => 0
	);
}
catch ( Exception $e ) {
	$responce = array(
		'status' => 1,
		'msg' => 'Unable to modify users pease try again later. If the error persists contact the administrator.'
	);
}

header('Content-Type: application/json');
echo json_encode($responce);