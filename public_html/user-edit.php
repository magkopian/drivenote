<?php require_once '../src/init.php';
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

// Check if user is signed in and is admin
if ( !$user->isSignedIn() || !$user->isAdmin() ) {
	header('HTTP/1.1 404 Not Found');
	die();
} // Check if the action and the users have been submitted
else if ( !isset($_POST['action'], $_POST['users']) || empty($_POST['action']) || empty($_POST['users']) ) {
	header('HTTP/1.1 404 Not Found');
	die();
} // Validate that the users argument is array and that action is "delete", "revoke-access", of "grant-read"
else if ( !is_array($_POST['users']) || !in_array($_POST['action'], array('delete', 'revoke-access', 'grant-read')) ) {
	header('HTTP/1.1 404 Not Found');
	die();
}

// Validate that the user IDs are all positive integers
$userIds = array();
foreach ( $_POST['users'] as $userId ) {
	if ( ($userIds[] = (int) $userId) <= 0 ) {
		header('HTTP/1.1 404 Not Found');
		die();
	}
}

try {
	
	$not_modified = array();
	$msg = '';
	
	if ( $_POST['action'] == 'delete' ) {

		foreach ( $userIds as $userId ) {
			$where[] = [
				'field'    => 'user_id',
				'operator' => '=',
				'value'    => $userId,
				'restrict' => false
			];
		}

		$users = $user->search($where, 0, 9999, ['user_id', 'google_email', 'is_admin']);

		if ( !empty($users) ) {

			$drivePermissions = $drive->getFilePermissions(DIRECTORY_ID);

			$userPermissions = array();
			foreach ( $drivePermissions as $drivePermission ) {
				$userPermissions[$drivePermission->getEmailAddress()] = $drivePermission->getRole();
				$userPermissionsIds[$drivePermission->getEmailAddress()] = $drivePermission->getId();
			}

			foreach ( $users['records'] as $user_data ) {

				if ( $user->getUserId() == $user_data['user_id'] ) {
					$msg .= 'You can\'t delete yourself!' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else if ( $user->getAdminAccessLevel() > 0 && $user_data['is_admin'] ) {
					$msg .= 'The user ' . $user_data['google_email'] . ' is an administrator and you don\'t have the right to delete administrators.' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else if ( isset($userPermissions[$user_data['google_email']]) && !empty($userPermissions[$user_data['google_email']]) ) {
					$msg .= 'The user ' . $user_data['google_email'] . ' already has access to the drive, you need to revoke it first.' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else {
					$user->delete($user_data['user_id']);
				}

			}

		}

	}
	else if ( $_POST['action'] == 'revoke-access' || $_POST['action'] == 'grant-read' ) {
		
		foreach ( $userIds as $userId ) {
			$where[] = array (
				'field' => 'user_id',
				'operator' => '=',
				'value' => $userId,
				'restrict' => false
			);
		}
		
		$users = $user->search($where, 0, 9999, array('user_id', 'google_email', 'is_admin'));
		
		if ( !empty($users) ) {
			
			$drivePermissions = $drive->getFilePermissions(DIRECTORY_ID);
			
			$userPermissions = array();
			foreach ( $drivePermissions as $drivePermission ) {
				$userPermissions[$drivePermission->getEmailAddress()] = $drivePermission->getRole();
				$userPermissionsIds[$drivePermission->getEmailAddress()] = $drivePermission->getId();
			}
			
			foreach ( $users['records'] as $user_data ) {
				
				// Revoke access
				if ( $_POST['action'] == 'revoke-access' ) {
					if ( $user->getUserId() == $user_data['user_id'] ) {
						$msg .= 'You can\'t revoke access to the drive from yourself!' . "\n";
						$not_modified[] = $user_data['user_id'];
					}
					else if ( $user->getAdminAccessLevel() > 0 && $user_data['is_admin'] ) {
						$msg .= 'The user ' . $user_data['google_email'] . ' is an administrator and you don\'t have the right to change the permissions of administrators.' . "\n";
						$not_modified[] = $user_data['user_id'];
					}
					else if ( !isset($userPermissions[$user_data['google_email']]) || empty($userPermissions[$user_data['google_email']]) ) {
						$msg .= 'The user ' . $user_data['google_email'] . ' already doesn\'t have access to the drive.' . "\n";
						$not_modified[] = $user_data['user_id'];
					}
					else {
						$drive->revokeAccess($userPermissionsIds[$user_data['google_email']], DIRECTORY_ID);
					}
				}// Grant read access
				else if ( $_POST['action'] == 'grant-read' ) {
					if ( $user->getUserId() == $user_data['user_id'] ) {
						$msg .= 'You can\'t change your own permissions to the drive!' . "\n";
						$not_modified[] = $user_data['user_id'];
					}
					else if ( isset($userPermissions[$user_data['google_email']]) && in_array($userPermissions[$user_data['google_email']], array('reader', 'writer', 'owner')) ) {
						$msg .= 'The user ' . $user_data['google_email'] . ' already has read access to the drive.' . "\n";
						$not_modified[] = $user_data['user_id'];
					}
					else if ( $user->getAdminAccessLevel() > 0 && $user_data['is_admin'] ) {
						$msg .= 'The user ' . $user_data['google_email'] . ' is an administrator and you don\'t have the right to change the permissions of administrators.' . "\n";
						$not_modified[] = $user_data['user_id'];
					}
					else {
						$drive->grantReadAccess($user_data['google_email'], DIRECTORY_ID);
					}
				}
				
			}
			
		}

	}
	
	$responce = array(
		'status' => 0,
		'notModified' => $not_modified,
		'msg' => nl2br(htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'), false)
	);
	
}
catch ( Exception $e ) {
	$logger = new ExceptionLogger();
	$logger->error($e);
	$responce = array(
		'status' => 1,
		'msg' => 'Unable to modify users pease try again later. If the error persists contact the administrator.'
	);
}

header('Content-Type: application/json');
echo json_encode($responce);
