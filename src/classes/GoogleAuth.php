<?php

class GoogleAuth extends GoogleService {
	protected $sessionName = 'WEBAPP_ACCESS_TOKEN';
	protected $user = null;
	
	public function __construct ( Google_Client $client, User $user, Database $db ) {
		
		$this->user = $user;
		$this->db = $db;
		
		$this->client = $client;
		$this->client->setClientId(WEBAPP_CLIENT_ID);
		$this->client->setClientSecret(WEBAPP_SECRET);
		$this->client->setRedirectUri(WEBAPP_REDIRECT_URI);
		$this->client->setScopes(WEBAPP_API_SCOPE);
		
		// If we have the access token from a session
		if ( isset($_SESSION[$this->sessionName]) && !empty($_SESSION[$this->sessionName]) ) {
			
			// We set it
			try {
				$this->client->setAccessToken($_SESSION[$this->sessionName]);
			}
			catch ( Google_Auth_Exception $e ) {
				// Log the error
				//...
					
				throw  new Exception('Unable to set access token from session variable.');
			}
				
			// And we check if it's still valid ( >= PHP 5.5: Should go inside finally block)
			if ( $this->client->isAccessTokenExpired() ) {
				unset($_SESSION[$this->sessionName]);
				$this->user->destroy();
			}
			else {
				$this->accessToken = $_SESSION[$this->sessionName];
			}
			
		}
		
	}
	
	public function getAuthURL () {
	
		return $this->client->createAuthUrl();
	
	}
	
	public function signIn ( $code ) {
		
		// If user already signed in
		if ( $this->accessToken !== null ) {
			return false;
		}
		
		try {
			$this->client->authenticate($code);
			
			$this->accessToken = $this->client->getAccessToken();
			$this->client->setAccessToken($this->accessToken);
			
			// If access token is expired
			if ( $this->client->isAccessTokenExpired() ) {
				$this->accessToken = null;
				return false;
			}
			else {
				// Store user data into database
				$userId = $this->addUser();
		
				// Instantiate an authenticated user
				$this->user->instantiate($userId);
				
				// Register the session
				$_SESSION[$this->sessionName] = $this->accessToken;
				
				return true;
			}
		}
		catch ( Google_Auth_Exception $e ) {
			// Log the error
			//...
			
			throw  new Exception('Unable to exchange code for access token.');
		}
		
	}
	
	public function signOut () {
	
		// User already singed out
		if ( $this->accessToken === null ) {
			return false;
		}
		
		unset($_SESSION[$this->sessionName]);
		$this->accessToken = null;
		$this->user->destroy();
		
		return true;
		
	}
	
	private function addUser () {
		
		try {
			$userData = $this->client->verifyIdToken()->getAttributes();
			
			$query = 'INSERT INTO `user` (`google_id`, `google_email`)
					  VALUES (:google_id, :google_email)
					  ON DUPLICATE KEY UPDATE `user_id` = LAST_INSERT_ID(`user_id`)'; // On duplicate google_id do nothing
			
			$preparedStatement = $this->db->prepare($query);
			
			$preparedStatement->execute( array(
				':google_id' => $userData['payload']['sub'],
				':google_email' => $userData['payload']['email']
			));
			
			return $this->db->lastInsertId();
		}
		catch ( Google_AuthException $e ) {
			// Log the error
			//...
			
			throw new Exception('Auth error, unable to verify id_token.');
		}
		catch ( PDOException $e ) {
			// Log the error
			//...

			throw new Exception('Database error, unable to insert user.');
		}
		
	}
	
}