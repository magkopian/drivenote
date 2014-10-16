<?php require_once '../includes/init.php';

// First, we authorize the application
if ( isset($_SESSION['service_token']) ) {
	$_SESSION['service_token'] = api_auth($googleClient, $_SESSION['service_token']);
}
else {
	$_SESSION['service_token'] = api_auth($googleClient);
}

// At this point the authentication process is complete. Now we can use the API.
$service = new Google_Service_Drive($googleClient);

// Grant read access to root directory
//api_grant_read_access($service, '<user_gmail_address>');

// Revoke read access to root directory
//api_revoke_read_access($service, '<user_gmail_address>');

// List all files
api_list_files($service);