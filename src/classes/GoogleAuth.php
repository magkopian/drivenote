<?php
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

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
				$logger = new ExceptionLogger();
				$logger->error($e);
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
			$logger = new ExceptionLogger();
			$logger->error($e);
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
			
			$query = 'SELECT `user_id` FROM `user` WHERE `google_id` = :google_id';
			
			$preparedStatement = $this->db->prepare($query);
				
			$preparedStatement->execute( array(
				':google_id' => $userData['payload']['sub']
			));
			
			if ( $preparedStatement->rowCount() == 0 ) {
				$query = 'INSERT INTO `user` (`google_id`, `google_email`)
						  VALUES (:google_id, :google_email)';
					
				$preparedStatement = $this->db->prepare($query);
					
				$preparedStatement->execute( array(
					':google_id' => $userData['payload']['sub'],
					':google_email' => $userData['payload']['email']
				));
					
				return $this->db->lastInsertId();
			}

			$res = $preparedStatement->fetch(PDO::FETCH_ASSOC);
			return $res['user_id'];
			
		}
		catch ( Google_AuthException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Auth error, unable to verify id_token.');
		}
		catch ( PDOException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable to insert user.');
		}
		
	}
	
}