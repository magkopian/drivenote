<?php

require_once '../vendor/autoload.php';
require_once '../includes/config.php';
require_once '../includes/func.php';

session_start();

// First, we authorize the application
$client = new Google_Client();
$client->setApplicationName('CSE Google Drive');

if ( isset($_SESSION['service_token']) ) {
	$_SESSION['service_token'] = api_auth($client, $_SESSION['service_token']);
}
else {
	$_SESSION['service_token'] = api_auth($client);
}

// At this point the authentication process is complete. Now we can use the API.
$service = new Google_Service_Drive($client);

// Grant read access to root directory
//api_grant_read_access($service, '<user_gmail_address>');

// Revoke read access to root directory
//api_revoke_read_access($service, '<user_gmail_address>');

// List all files
api_list_files($service);