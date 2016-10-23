<?php
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

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
		
		if ( ($key = file_get_contents(SERVICE_KEY_FILENAME)) === false ) {
			throw new Exception('Unable to open service private key file');
		}
		$credentials = new Google_Auth_AssertionCredentials(
			SERVICE_ACCOUNT_NAME, 
			array(SERVICE_API_SCOPE), 
			$key,
			SERVICE_KEY_SECRET
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
	
	public function grantReadAccess ( $googleEmail, $fileId ) {
		
		// Create a new permission object to give read access to a user
		$permission = new Google_Service_Drive_Permission();
		$permission->setRole('reader');
		$permission->setType('user');
		$permission->setEmailAddress($googleEmail);
	
		// Apply the permission to the file/directory
		$permissions = $this->service->permissions;
		$permissions->create($fileId, $permission);
	
	}
	
	public function revokeAccess ( $permissionId, $fileId ) {

		$this->service->permissions->delete($fileId, $permissionId);
	
	}
	
	public function redirect ( $fileId ) {
		
		$fileMetadata = $this->getFileMetadata($fileId);
		if ( !isset($fileMetadata['driveLink']) || empty($fileMetadata['driveLink']) ) {
			return false;
		}
		
		header('Location: ' . $fileMetadata['driveLink']);
		
	}
	
	public function getFileURL ( $fileId ) {
		
		$fileMetadata = $this->getFileMetadata($fileId);

		if ( !isset($fileMetadata['driveLink']) || empty($fileMetadata['driveLink']) ) {

			return false;
		}
		
		return $fileMetadata['driveLink'];
	}
	
	public function getFileMetadata ( $fileId ) {

		$file = $this->service->files->get($fileId, array('fields' => 'name,description,mimeType,webViewLink,webContentLink'));

		return array (
			'filename' => $file->getName(),
			'description' => $file->getDescription(),
			'mimeType' => $file->getMimeType(),
			'driveLink' => $file->getWebViewLink(),
			'downloadUrl' => $file->getWebContentLink()
		);

	}
	
	public function getFilePermissions ( $fileId ) {
		
		try {
			$permissions = $this->service->permissions->listPermissions($fileId, array('fields' => 'kind,permissions'));
			return $permissions->getPermissions();
		}
		catch ( Google_Exception $e ){
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Unable to get file permissions.');
		}
		
	}
	
	public function listFiles () {
	
		echo '<pre>', print_r($this->service->files->listFiles()), '</pre>';
	
	}
	
	public function getQuota () {
		
		$about = $this->service->about->get();

		return array (
			'total' => (int) $about->getQuotaBytesTotal(),
			'used' => (int) $about->getQuotaBytesUsed(),
			'available' => (int) $about->getQuotaBytesTotal() - $about->getQuotaBytesUsed()
		);
		
	}
	
}
