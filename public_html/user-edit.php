<?php require_once '../src/init.php';
/************************************************\
 * Copyright (c) 2016 Manolis Agkopian          *
 * See the file LICENCE for copying permission. *
\************************************************/

// Check if user is signed in and is admin
if ( !$user->isSignedIn() || !$user->isAdmin() ) {
	header('HTTP/1.1 404 Not Found');
	die();
} // Check if the action and the users have been submitted
else if ( !isset($_POST['action'], $_POST['users']) || empty($_POST['action']) || empty($_POST['users']) ) {
	header('HTTP/1.1 404 Not Found');
	die();
} // Validate that the users argument is array and that action is "delete", "revoke-access", of "grant-read"
else if ( !is_array($_POST['users']) || !in_array($_POST['action'], ['delete', 'revoke-access', 'grant-read', 'revoke-membership', 'grant-membership']) ) {
	header('HTTP/1.1 404 Not Found');
	die();
}

$redirectTo = [
	'delete'            => 'users.php',
	'revoke-access'     => 'users.php',
	'grant-read'        => 'users.php',
	'revoke-membership' => 'users.php',
	'grant-membership'  => 'members.php'
];

// Validate that the user IDs are all positive integers
$userIds = [];
foreach ( $_POST['users'] as $userId ) {
	if ( ($userIds[] = (int) $userId) <= 0 ) {
		header('HTTP/1.1 404 Not Found');
		die();
	}
}

try {

	$not_modified = [];
	$msg = '';

	foreach ( $userIds as $userId ) {
		$where[] = [
			'field'    => 'user_id',
			'operator' => '=',
			'value'    => $userId,
			'restrict' => false
		];
	}

	$users = $user->search($where, 0, 9999, ['user_id', 'google_email', 'is_admin', 'approved', 'verified']);

	if ( !empty($users) ) {

		$drivePermissions = $drive->getFilePermissions(DIRECTORY_ID);

		$userPermissions = [];
		foreach ( $drivePermissions as $drivePermission ) {
			$userPermissions[$drivePermission->getEmailAddress()] = $drivePermission->getRole();
			$userPermissionsIds[$drivePermission->getEmailAddress()] = $drivePermission->getId();
		}

		foreach ( $users['records'] as $user_data ) {

			// Delete user
			if ( $_POST['action'] == 'delete' ) {

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
				else if ( $user_data['approved'] ) {
					$msg .= 'The user ' . $user_data['google_email'] . ' is a member of the group, you need to revoke their membership first.' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else {
					$user->delete($user_data['user_id']);
				}

			} // Revoke access
			else if ( $_POST['action'] == 'revoke-access' ) {

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

			} // Grant read access
			else if ( $_POST['action'] == 'grant-read' ) {

				if ( $user->getUserId() == $user_data['user_id'] ) {
					$msg .= 'You can\'t change your own permissions to the drive!' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else if ( isset($userPermissions[$user_data['google_email']]) && in_array($userPermissions[$user_data['google_email']], ['reader', 'writer', 'owner']) ) {
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

			} // Remove group member
			else if ( $_POST['action'] == 'revoke-membership' ) {

				if ( $user->getUserId() == $user_data['user_id'] ) {
					$msg .= 'You can\'t revoke the membership from yourself!' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else if ( $user->getAdminAccessLevel() > 0 && $user_data['is_admin'] ) {
					$msg .= 'The user ' . $user_data['google_email'] . ' is an administrator and you don\'t have the right to revoke the membership of administrators.' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else if ( !$user_data['approved'] ) {
					$msg .= 'The user ' . $user_data['google_email'] . ' already isn\'t a member of the group.' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else {
					$user->removeMember($user_data['user_id']);
				}

			} // Add group member
			else if ( $_POST['action'] == 'grant-membership' ) {

				if ( $user->getUserId() == $user_data['user_id'] ) {
					$msg .= 'You can\'t mark yourself as a member!' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else if ( $user->getAdminAccessLevel() > 0 && $user_data['is_admin'] ) {
					$msg .= 'The user ' . $user_data['google_email'] . ' is an administrator and you don\'t have the right to mark administrators as members.' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else if ( !$user_data['verified'] ) {
					$msg .= 'The user ' . $user_data['google_email'] . ' is not verified and thus cannot be added to the group.' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else if ( $user_data['approved'] ) {
					$msg .= 'The user ' . $user_data['google_email'] . ' is already a member of the group.' . "\n";
					$not_modified[] = $user_data['user_id'];
				}
				else {
					$user->addMember($user_data['user_id']);
				}

			}

		}

	}

	$response = [
		'status'      => 0,
		'notModified' => $not_modified,
		'msg'         => nl2br(htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'), false)
	];

}
catch ( Exception $e ) {
	$logger = new ExceptionLogger();
	$logger->error($e);
	$response = [
		'status' => 1,
		'msg'    => 'Unable to modify users please try again later. If the error persists contact the administrator.'
	];
}

if ( isset($_POST['ajax']) && $_POST['ajax'] == 0 ) {

	if ( $response['status'] == 0 ) {
		Notifier::push('success', 'The users were updated successfully!');
	}
	else {
		Notifier::push('error', $response['msg']);
	}

	header('Location: ' . $redirectTo[$_POST['action']]);
	die();
}

header('Content-Type: application/json');
echo json_encode($response);
