<?php
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

class Verifier {
	private $user = null;
	private $db = null;
	
	public function __construct ( User $user, Database $db ) {
		
		$this->user = $user;
		$this->db = $db;
		
	}
	
	
	public function getVerifyURL () {
	
		if ( $this->user->isSignedIn() === false ) {
			throw new Exception('Cannot generate verify URL if user is not signed in.');
		}
		
		$token = md5(uniqid(null, true));
		$this->user->setVerifyToken($token);
		
		return 'http://' . DOMAIN . '/verify-account.php?token=' . $token . '&uid=' . $this->user->getUserId();
	
	}
	
	public function isTokenExpired ( $userId, $token ) {
	
		$query = 'SELECT `user_id` FROM `user` WHERE `user_id` = :user_id AND `token` = :token';
	
		try {
			$preparedStatement = $this->db->prepare($query);
	
			$preparedStatement->execute( array(
				':token' => $token,
				':user_id' => $userId
			));
				
			if ( $preparedStatement->rowCount() == 0 ) {
				return true;
			}
				
			return false;
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
	
			throw new Exception('Database error, unable to check if verify token is expired.');
		}
	
	}
	
	public function isEmailVerified ( $academicEmail ) {
	
		$query = 'SELECT `user_id` FROM `user` WHERE `academic_email` = :academic_email AND `verified` = 1';
	
		try {
			$preparedStatement = $this->db->prepare($query);
	
			$preparedStatement->execute( array(
				':academic_email' => $academicEmail
			));
				
			if ( $preparedStatement->rowCount() == 0 ) {
				return false;
			}
				
			return true;
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
	
			throw new Exception('Database error, unable to check if verified email exists.');
		}
	
	}
	
	public function verify ( $userId, $token ) {
	
		$query = 'UPDATE `user` SET `verified` = 1, `token` = NULL
				  WHERE `user_id` = :user_id AND `token` = :token';
	
		try {
			$preparedStatement = $this->db->prepare($query);
	
			$preparedStatement->execute( array(
				':token' => $token,
				':user_id' => $userId
			));
		}
		catch ( PDOException $e ) {
			// Log the error
			//...
	
			throw new Exception('Database error, unable to verify email.');
		}
	
	}
	
}