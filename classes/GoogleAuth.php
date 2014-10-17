<?php

class GoogleAuth extends GoogleService {
	protected $sessionName = 'WEBAPP_ACCESS_TOKEN';
	
	public function __construct ( Google_Client $client ) {
		
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
		
		$this->db = Database::getInstance();
		
	}
	
	public function isSignedIn () {
		
		if ( $this->accessToken === null ) {
			return false;
		}
		
		return true;
		
	}
	
	public function signIn ( $code ) {
		
		// If user already signed in
		if ( $this->accessToken !== null ) {
			return false;
		}
		
		$this->client->authenticate($code);
		
		$this->accessToken = $this->client->getAccessToken();
		$this->client->setAccessToken($this->accessToken);
		
		// If access token is expired
		if ( $this->client->isAccessTokenExpired() ) {
			$this->accessToken = null;
			return false;
		}
		
		// Store user data into database
		$this->addUser();
			
		// Register the session
		$_SESSION[$this->sessionName] = $this->accessToken;
			
		return true;
		
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
	
	public function getAuthURL () {
	
		return $this->client->createAuthUrl();
	
	}
	
	private function addUser () {
		
		try {
			$userData = $this->client->verifyIdToken()->getAttributes();
			
			$query = 'INSERT INTO `user` (`google_id`, `google_email`)
					  VALUES (:google_id, :google_email)
					  ON DUPLICATE KEY UPDATE `google_id` = :google_id_2'; // On duplicate google_id do nothing
			
			$preparedStatement = $this->db->prepare($query);
			
			$preparedStatement->execute( array(
				':google_id' => $userData['payload']['id'],
				':google_email' => $userData['payload']['email'],
				':google_id_2' => $userData['payload']['id']
			));
		}
		catch ( Google_AuthException $e ) {
			// Log the error
			//...
			
			throw new Exception('Internal error, unable to verify id_token. Please contact the administrator.');
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
				
			throw new Exception('Internal error, unable to insert user. Please contact the administrator.');
		}
		
	}
	
}