<?php require_once '../src/init.php';
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

if ( !$user->isSignedIn() || !$user->isAdmin() ) {
	header('HTTP/1.1 404 Not Found');
	die();
}
else if ( !isset($_POST['action'], $_POST['users']) || empty($_POST['action']) || empty($_POST['users']) ) {
	header('HTTP/1.1 404 Not Found');
	die();
}
else if ( !is_array($_POST['users']) || !in_array($_POST['action'], array('delete', 'revoke-access', 'grant-read')) ) {
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
	
	// TODO: Check access level
	
	if ( $_POST['action'] == 'delete' ) {
		$not_deleted = $user->delete($users);
		$msg = '';
		
		if ( count($not_deleted) > 0 ) {
			$msg = 'You don\'t  have the right to delete the user';
			if ( count($not_deleted) > 1 ) {
				$msg .= 's';
			}
			$msg .= ': ' . implode(', ', $not_deleted);
		}
		
		$responce = array(
			'status' => 0,
			'notDeleted' => $not_deleted,
			'msg' => $msg
		);
	}
	else if ( $_POST['action'] == 'revoke-access' ) {
		// Revoke read access
		
		$responce = array(
			'status' => 0
		);
	}
	else if ( $_POST['action'] == 'grant-read' ) {
		// Grant read access
		
		$responce = array(
			'status' => 0
		);
	}
	
}
catch ( Exception $e ) {
	$responce = array(
		'status' => 1,
		'msg' => 'Unable to modify users pease try again later. If the error persists contact the administrator.'
	);
}

header('Content-Type: application/json');
echo json_encode($responce);