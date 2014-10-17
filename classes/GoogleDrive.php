<?php

class GoogleDrive extends GoogleService {
	protected $sessionName = 'SERVICE_ACCESS_TOKEN';
	protected $service = null;
	
	public function __construct ( Google_Client $client ) {
		
		$this->client = $client;
		
		// If we have an access token, we can carry on. Otherwise, 
		// we'll get one with the help of an assertion credential.
		if ( isset($_SESSION[$this->sessionName]) && !empty($_SESSION[$this->sessionName]) ) {
			
			$this->client->setAccessToken($_SESSION[$this->sessionName]); // We set it
			
		}
		
		$key = file_get_contents(SERVICE_KEY_FILENAME);
		$credentials = new Google_Auth_AssertionCredentials(
			SERVICE_ACCOUNT_NAME, 
			array(SERVICE_API_SCOPE), 
			$key
		);
		$this->client->setAssertionCredentials($credentials);
		
		// If the token has expired we refresh it
		if ( $this->client->getAuth()->isAccessTokenExpired() ) {
			$this->client->getAuth()->refreshTokenWithAssertion($credentials);
		}
		
		$this->accessToken = $this->client->getAccessToken();
		$_SESSION[$this->sessionName] = $this->accessToken;
		
		$this->service = new Google_Service_Drive($this->client);
		
	}
	
	public function grantReadAccess ( $googleEmail ) {
	
		// Create a new permition object to give read access to a user
		$permition = new Google_Service_Drive_Permission();
		$permition->setRole('reader');
		$permition->setType('user');
		$permition->setValue($googleEmail);
	
		// Apply the permition to the file/directory
		$permissions = $this->service->permissions;
		$permissions->insert(DIRECTORY_ID, $permition);
	
	}
	
	public function revokeReadAccess ( $googleEmail ) {
	
		$permissionId = $this->service->permissions->getIdForEmail($googleEmail);
	
		$this->service->permissions->delete(DIRECTORY_ID, $permissionId->getId());
	
	}
	
	public function listFiles () {
	
		echo '<pre>', print_r($this->service->files->listFiles()), '</pre>';
	
	}
	
}