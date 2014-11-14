<?php
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

class User {
	private $userId = 0;
	private $googleId = null;
	private $googleEmail = null;
	private $academicEmail = null;
	private $admin = false;
	private $adminAccessLevel = null;
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
	
	// Should get called only by an instance of GoolgeAuth class
	public function instantiate ( $userId ) {
		
		$this->userId = $userId;
		$_SESSION[$this->sessionName] = $this->userId;
		$this->fetch();
		
	}
	
	// Should get called only by an instance of GoolgeAuth class
	public function destroy () {
		
		$this->userId = null;
		unset($_SESSION[$this->sessionName]);
		
		$this->userId = 0;
		$this->googleId = 0;
		$this->googleEmail = null;
		$this->academicEmail = null;
		$this->admin = false;
		$this->adminAccessLevel = null;
		$this->verified = 0;
		$this->verifyToken = null;
		
	}
	
	public function isSignedIn () {
		
		if ( $this->userId > 0 ) {
			return true;
		}
		
		return false;
		
	}
	
	public function isAdmin () {
	
		return $this->admin;
	
	}
	
	public function isVerified () {
		
		return $this->verified;
		
	}
	
	public function getUserId () {
		
		return $this->userId;
		
	}
	
	public function getGoogleEmail () {
	
		return $this->googleEmail;
	
	}
	
	public function getAcademicEmail () {
	
		return $this->academicEmail;
	
	}
	
	public function setAcademicEmail ( $academicEmail ) {
		
		$this->academicEmail = $academicEmail;
		
	}
	
	// Should only get called by an instance of Verify class
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
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable to update user.');
		}
		
	}
	
	private function fetch () {
		
		$query = 'SELECT `user`.`user_id`, `google_id`, `google_email`, `academic_email`, `verified`, `token`,
				  CASE WHEN `admin`.`user_id` IS NOT NULL 
					   THEN 1
					   ELSE 0
				  END AS `is_admin`, `access_level`
				  FROM `user` 
				  LEFT OUTER JOIN `admin`
				  ON `user`.`user_id` = `admin`.`user_id`
				  WHERE `user`.`user_id` = :user_id';
		
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
				$this->admin = (bool) $res['is_admin'];
				$this->verified = (bool) $res['verified'];
				$this->verifyToken = $res['token'];
				
				if ( $this->admin === true ) {
					$this->adminAccessLevel = (int) $res['access_level'];
				}
				
			}
		}
		catch ( PDOException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable fetch user.');
		}
		
	}
	
}