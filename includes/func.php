<?php

function api_auth ( Google_Client $client, $token = null ) {
	
	// If we have an access token, we can carry on. Otherwise, 
	// we'll get one with the help of an assertion credential.
	
	if ( $token !== null ) {
		$client->setAccessToken($token);
	}
	
	$key = file_get_contents(KEY_FILENAME);
	$credentials = new Google_Auth_AssertionCredentials(SERVICE_ACCOUNT_NAME, array(API_SCOPE), $key);
	$client->setAssertionCredentials($credentials);
	
	if( $client->getAuth()->isAccessTokenExpired() ) {
		$client->getAuth()->refreshTokenWithAssertion($credentials);
	}
	
	return $client->getAccessToken();
	
}

function api_grant_read_access ( Google_Service_Drive $service, $gmail_address ) {
	
	// Create a new permition object to give read access to a user
	$permition = new Google_Service_Drive_Permission();
	$permition->setRole('reader');
	$permition->setType('user');
	$permition->setValue($gmail_address);
	
	// Apply the permition to the file/directory
	$permissions = $service->permissions;
	$permissions->insert(DIRECTORY_ID, $permition);
	
}

function api_revoke_read_access ( Google_Service_Drive $service, $gmail_address ) {

	$permissionId = $service->permissions->getIdForEmail($gmail_address);

	$service->permissions->delete(DIRECTORY_ID, $permissionId->getId());

}

function api_list_files ( Google_Service_Drive $service ) {
	
	$files = $service->files;
	echo '<pre>', print_r($files->listFiles()), '</pre>';
	
}