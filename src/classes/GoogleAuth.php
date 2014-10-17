<?php

class GoogleAuth extends GoogleService {
	protected $sessionName = 'WEBAPP_ACCESS_TOKEN';
	protected $userIdSession = 'USER_ID';
	
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
		$_SESSION[$this->userIdSession] = $this->addUser();

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
		unset($_SESSION[$this->userIdSession]);
		$this->accessToken = null;
		return true;
		
	}
	
	public function academicEmailExists ( $academicEmail ) {
		
		$query = 'SELECT `user_id` FROM `user` WHERE `academic_email` = :academic_email AND `user_id` != :user_id';
		
		try {
			$preparedStatement = $this->db->prepare($query);
		
			$preparedStatement->execute( array(
				':academic_email' => $academicEmail,
				':user_id' => $_SESSION[$this->userIdSession]
			));
				
			if ( $preparedStatement->rowCount() != 0 ) {
				return true;
			}
			
			return false;
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
		
			throw new Exception('Database error, unable check if academic email exists.');
		}
		
	}
	
	public function getAuthURL () {
	
		return $this->client->createAuthUrl();
	
	}
	
	public function getVerifyURL ( $academicEmail ) {
		
		if ( $this->isSignedIn() === false ) {
			throw new Exception('GoogleAuth::getVerifyURL method got called while user wasn\'t authenticated');
		}
		
		$verifyEmailToken = md5(uniqid(null, true));
		
		$query = 'UPDATE `user` SET `academic_email` = :academic_email, `token` = :token 
				  WHERE `user_id` = :user_id';
		
		try {	
			$preparedStatement = $this->db->prepare($query);
				
			$preparedStatement->execute( array(
				':token' => $verifyEmailToken,
				':academic_email' => $academicEmail,
				':user_id' => $_SESSION[$this->userIdSession]
			));
			
			return 'http://' . DOMAIN . '/verify-account.php?token=' . $verifyEmailToken . '&uid=' . $_SESSION[$this->userIdSession];
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
		
			throw new Exception('Database error, unable to insert email verify token.');
		}
		
	}
	
	private function addUser () {
		
		try {
			$userData = $this->client->verifyIdToken()->getAttributes();
			
			$query = 'INSERT INTO `user` (`google_id`, `google_email`)
					  VALUES (:google_id, :google_email)
					  ON DUPLICATE KEY UPDATE `user_id` = LAST_INSERT_ID(`user_id`)'; // On duplicate google_id do nothing
			
			$preparedStatement = $this->db->prepare($query);
			
			$preparedStatement->execute( array(
				':google_id' => $userData['payload']['id'],
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