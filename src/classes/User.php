<?php

class User {
	private $userId = 0;
	private $googleId = null;
	private $googleEmail = null;
	private $academicEmail = null;
	private $verified = false;
	private $verifyToken = null;
	private $sessionName = 'USER_ID';
	private $db = null;
	
	public function __construct ( Database $db ) {
		
		$this->db = $db;
		
		if ( isset($_SESSION[$this->sessionName]) ) {
			$this->userId = $_SESSION[$this->sessionName];
			$this->fetch();
		}
		
	}
	
	// Should be called only by a GoolgeAuth instance
	public function instantiate ( $userId ) {
		
		$this->userId = $userId;
		$_SESSION[$this->sessionName] = $this->userId;
		$this->fetch();
		
	}
	
	// Should be called only by a GoolgeAuth instance
	public function destroy () {
		
		$this->userId = null;
		unset($_SESSION[$this->sessionName]);
		
		$this->userId = 0;
		$this->googleId = 0;
		$this->googleEmail = null;
		$this->academicEmail = null;
		$this->verified = 0;
		$this->verifyToken = null;
		
	}
	
	public function isSignedIn () {
		
		if ( $this->userId > 0 ) {
			return true;
		}
		
		return false;
		
	}
	
	public function isVerified () {
		
		return $this->verified;
		
	}
	
	public function getUserId () {
		
		return $this->userId;
		
	}
	
	public function getAcademicEmail () {
	
		return $this->academicEmail;
	
	}
	
	public function setAcademicEmail ( $academicEmail ) {
		
		$this->academicEmail = $academicEmail;
		
	}
	
	public function setVerifyToken ( $verifyToken ) {
	
		$this->verifyToken = $verifyToken ;

	}
	
	public function save () {
		
		$query = 'UPDATE `user` SET 
				 `google_id` = :google_id,
				 `google_email` = :google_email,
				 `academic_email` = :academic_email,
				 `verified` = :verified,
				 `token` = :token
				  WHERE `user_id` = :user_id';
		
		try {
			$preparedStatement = $this->db->prepare($query);
		
			$preparedStatement->execute( array(
					':google_id' => $this->googleId,
					':google_email' => $this->googleEmail,
					':academic_email' => $this->academicEmail,
					':verified' => $this->verified,
					':token' => $this->verifyToken,
					':user_id' => $this->userId
			));
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
		
			throw new Exception('Database error, unable to update user.');
		}
		
	}
	
	private function fetch () {
		
		$query = 'SELECT `user_id`, `google_id`, `google_email`, `academic_email`, `verified`, `token` 
				  FROM `user` WHERE `user_id` = :user_id';
		
		try {
			$preparedStatement = $this->db->prepare($query);
		
			$preparedStatement->execute( array(
				':user_id' => $this->userId
			));
		
			if ( $preparedStatement->rowCount() != 0 ) {
				
				$res = $preparedStatement->fetch(PDO::FETCH_ASSOC);
				
				$this->googleId = $res['google_id'];
				$this->googleEmail = $res['google_email'];
				$this->academicEmail = $res['academic_email'];
				$this->verified = (bool) $res['verified'];
				$this->verifyToken = $res['token'];
				
			}
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
		
			throw new Exception('Database error, unable fetch user.');
		}
		
	}
	
}