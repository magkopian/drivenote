<?php

class GoogleAuth {
	private $client = null;
	private $accessToken = null;
	private $sessionName = 'WEBAPP_ACCESS_TOKEN';
	
	public function __construct( Google_Client $client, Database $db = null ) {
		
		$this->client = $client;
		
		$this->client->setClientId(WEBAPP_CLIENT_ID);
		$this->client->setClientSecret(WEBAPP_SECRET);
		$this->client->setRedirectUri(WEBAPP_REDIRECT_URI);
		$this->client->setScopes(WEBAPP_API_SCOPE);
		
		// If we have the access token from a session
		if ( isset($_SESSION[$this->sessionName]) && !empty($_SESSION[$this->sessionName]) ) {
			
			// We set it
			$this->client->setAccessToken($_SESSION[$this->sessionName]);
			
			// And we check if it's still valid
			if ( $this->client->isAccessTokenExpired() ) {
				unset($_SESSION[$this->sessionName]);
			}
			else {
				$this->accessToken = $_SESSION[$this->sessionName];
			}
			
		}
		
		if ( $db === null ) {
			
			$this->db = Database::getInstance();
			
		}
		else {
			
			$this->db = $db;
			
		}
		
	}
	
	public function isSignedIn () {
		
		if ( $this->accessToken === null ) {
			return false;
		}
		
		return true;
		
	}
	
	public function getAuthURL () {
		
		return $this->client->createAuthUrl();
		
	}
	
	public function signIn ( $code ) {
		
		// User already signed in
		if ( $this->accessToken !== null ) {
			return false;
		}
		
		$this->client->authenticate($code);
		
		$this->accessToken = $this->client->getAccessToken();
		$this->client->setAccessToken($this->accessToken);
		
		if ( $this->client->isAccessTokenExpired() === false ) {
			
			$_SESSION[$this->sessionName] = $this->accessToken;
			
			// Store user data into database
			$this->addUser();
			
			return true;
			
		}
		
		$this->accessToken = null;
		return false;
		
	}
	
	public function signOut () {
	
		// User already singed out
		if ( $this->accessToken === null ) {
			return false;
		}
		
		unset($_SESSION[$this->sessionName]);
		$this->accessToken = null;
		return true;
		
	}
	
	private function addUser () {
		
		$userData = $this->client->verifyIdToken()->getAttributes();
		
		$query = 'INSERT INTO `user` (`google_id`, `google_email`)
				  VALUES (:google_id, :google_email)
				  ON DUPLICATE KEY UPDATE `google_id` = :google_id_2'; // On duplicate google_id do nothing
		
		try {
			$preparedStatement = $this->db->prepare($query);
			
			$preparedStatement->execute( array(
				':google_id' => $userData['payload']['id'],
				':google_email' => $userData['payload']['email'],
				':google_id_2' => $userData['payload']['id']
			));
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
			
			throw new Exception('Internal error, unable to insert user.');
		}
		
	}
	
}