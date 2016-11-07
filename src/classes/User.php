<?php

/************************************************\
 * Copyright (c) 2016 Manolis Agkopian          *
 * See the file LICENCE for copying permission. *
\************************************************/

class User {

	private $userId = 0;
	private $googleId = null;
	private $googleEmail = null;
	private $academicEmail = null;
	private $admin = false;
	private $adminAccessLevel = null;
	private $verified = false;
	private $approved = false;
	private $verifyToken = null;
	private $sessionName = 'USER_ID';
	private $db = null;

	public function __construct ( Database $db ) {

		$this->db = $db;

		if ( isset($_SESSION[$this->sessionName]) ) {

			$this->userId = $_SESSION[$this->sessionName];
			$userData = $this->fetch($this->userId);

			if ( $userData === null || empty($userData) ) {
				throw new UserNotFoundException('Unable to fetch user, user with `user_id` ' . (int) $this->userId . ' could not be found');
			}
			else {
				$this->initialize($userData);
			}

		}

	}

	public function search ( $where, $offset, $limit, $fields = [] ) {

		if ( !$this->isAdmin() || $this->adminAccessLevel > 1 ) {
			throw new Exception('Only administrators have the right to call the User::search() method');
		}

		$offset = $offset < 0 ? 0 : (int) $offset;
		$limit = $limit < 1 ? 0 : (int) $limit;

		$values = [];
		$values[':offset'] = $offset;
		$values[':limit'] = $limit;

		if ( empty($where) ) {
			$whereClause = 'WHERE 1';
			$havingClause = '';
		}
		else {
			$havingClause = '';
			$whereClause = '';
			$cnt = 0;
			$first_where = true;

			foreach ( $where as $condition ) {

				if ( !array_key_exists('field', $condition) ||
					!array_key_exists('operator', $condition) ||
					!array_key_exists('value', $condition) ||
					empty($condition['field']) ||
					empty($condition['operator']) ||
					!in_array($condition['field'], ['user_id', 'google_id', 'google_email', 'academic_email', 'verified', 'approved', 'is_admin', 'access_level']) ||
					!in_array($condition['operator'], ['=', '!=', '>', '<', '>=', '<='])
				) {

					throw new Exception('Invalid $where argument supplied to User::search() method');
				}

				if ( $condition['field'] != 'is_admin' ) {

					if ( !$first_where ) {

						if ( array_key_exists('restrict', $condition) && $condition['restrict'] === false ) {

							$whereClause .= ' OR ';

						}
						else {

							$whereClause .= ' AND ';

						}

					}
					else {
						$whereClause = 'WHERE ';
					}

					if ( $condition['field'] == 'user_id' ) {
						$whereClause .= '`user`.';
					}

					$whereClause .= '`' . $condition['field'] . '` ' . $condition['operator'] . ' :' . $condition['field'] . '_' . $cnt;
					$first_where = false;

				}
				else {
					$havingClause = 'HAVING `' . $condition['field'] . '` ' . $condition['operator'] . ' :' . $condition['field'] . '_' . $cnt;
				}

				$values[':' . $condition['field'] . '_' . $cnt++] = $condition['value'];

			}

		}

		if ( empty($fields) ) {
			$fields = [
				'user_id',
				'google_id',
				'google_email',
				'academic_email',
				'verified',
				'approved',
				'is_admin',
				'access_level'
			];
		}

		$select = '';
		if ( !is_array($fields) ) {
			throw new Exception('$fields argument supplied to User::search() method needs to be array');
		}
		else {

			$first_select = true;

			foreach ( $fields as $field ) {

				if ( empty($field) || !in_array($field, ['user_id', 'google_id', 'google_email', 'academic_email', 'verified', 'approved', 'is_admin', 'access_level']) ) {

					throw new Exception('Invalid $fields argument supplied to User::search() method');

				}

				if ( !$first_select ) {
					$select .= ', ';
				}

				if ( $field != 'is_admin' ) {

					if ( $field == 'user_id' ) {
						$select .= '`user`.';
					}

					$select .= '`' . $field . '`';

				}
				else {
					$select .=
						'CASE WHEN `admin`.`user_id` IS NOT NULL 
							THEN 1
							ELSE 0
				  		 END AS `is_admin`';
				}

				$first_select = false;
			}

		}

		$query = 'SELECT SQL_CALC_FOUND_ROWS ' . $select . '
				  FROM `user` 
				  LEFT OUTER JOIN `admin`
				  ON `user`.`user_id` = `admin`.`user_id` ' .
			$whereClause .
			' LIMIT :offset, :limit ' .
			$havingClause;

		try {
			$preparedStatement = $this->db->prepare($query);
			$preparedStatement->execute($values);

			$statement = $this->db->query('SELECT FOUND_ROWS()');
			$totalUsers = (int) $statement->fetchColumn();

			if ( $preparedStatement->rowCount() != 0 ) {
				return [
					'total_users' => $totalUsers,
					'records'     => count($fields) > 1 ? $preparedStatement->fetchAll(PDO::FETCH_ASSOC) : $preparedStatement->fetchAll(PDO::FETCH_COLUMN)
				];
			}
			else {
				return [
					'total_users' => $totalUsers,
					'records'     => null
				];
			}
		}
		catch ( PDOException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable search users.');
		}

	}

	public function addMember ( $userId ) {

		$this->member($userId, true);

	}

	public function removeMember ( $userId ) {

		$this->member($userId, false);

	}

	private function member ( $userId, $approved ) {

		$query_member = 'UPDATE `user` SET `approved` = :approved WHERE `user_id` = :user_id AND `verified` = 1';

		$values = [
			':user_id'  => $userId,
			':approved' => $approved
		];

		try {
			$preparedStatement = $this->db->prepare($query_member);
			$preparedStatement->execute($values);
		}
		catch ( PDOException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable to change approved status of user.');
		}

	}

	public function delete ( $userId ) {

		// Only an admin with access level < 1 is able to delete other admins
		if ( $this->adminAccessLevel > 0 ) {
			$query_delete_user = 'DELETE FROM `user` WHERE `user_id` = :user_id AND `user_id` NOT IN ( SELECT `user_id` FROM `admin` )';
		}
		else {
			$query_delete_admin = 'DELETE FROM `admin` WHERE `user_id` = :user_id AND `user_id` != :user_id_self';
			$query_delete_user = 'DELETE FROM `user` WHERE `user_id` = :user_id AND `user_id` != :user_id_self';

			// Prevent admins to delete themselves
			$values[':user_id_self'] = $this->userId;
		}

		$values[':user_id'] = $userId;

		try {

			if ( $this->adminAccessLevel < 1 ) {
				$preparedStatement = $this->db->prepare($query_delete_admin);
				$preparedStatement->execute($values);
			}

			$preparedStatement = $this->db->prepare($query_delete_user);
			$preparedStatement->execute($values);

		}
		catch ( PDOException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable to delete user.');
		}

	}

	public function deleteMany ( $userIds ) {

		$where = 'WHERE `user_id` IN ( ';
		$values = [];
		$cnt = 0;

		foreach ( $userIds as $userId ) {
			if ( $cnt > 0 ) {
				$where .= ', ';
			}

			$where .= ':user_id_' . $cnt;
			$values[':user_id_' . $cnt++] = $userId;
		}
		$where .= ' )';

		// Only an admin with access level < 1 is able to delete other admins
		if ( $this->adminAccessLevel > 0 ) {
			$query_delete_user = 'DELETE FROM `user` ' . $where . ' AND `user_id` NOT IN ( SELECT `user_id` FROM `admin` )';
		}
		else {
			$query_delete_admin = 'DELETE FROM `admin` ' . $where . ' AND `user_id` != :user_id_self';
			$query_delete_user = 'DELETE FROM `user` ' . $where . ' AND `user_id` != :user_id_self';

			// Prevent admins to delete themselves
			$values[':user_id_self'] = $this->userId;
		}

		try {

			if ( $this->adminAccessLevel < 1 ) {
				$preparedStatement = $this->db->prepare($query_delete_admin);
				$preparedStatement->execute($values);
			}

			$preparedStatement = $this->db->prepare($query_delete_user);
			$preparedStatement->execute($values);

			$query_select_not_deleted = 'SELECT `user_id` FROM `user` ' . $where;
			if ( isset($values[':user_id_self']) ) {
				unset($values[':user_id_self']);
			}

			$preparedStatement = $this->db->prepare($query_select_not_deleted);
			$preparedStatement->execute($values);

			return $preparedStatement->fetchAll(PDO::FETCH_COLUMN);
		}
		catch ( PDOException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable to delete user(s).');
		}

	}

	// Should get called only by an instance of GoolgeAuth class
	public function instantiate ( $userId ) {

		$this->userId = $userId;
		$_SESSION[$this->sessionName] = $this->userId;
		$userData = $this->fetch($this->userId);

		if ( $userData === null || empty($userData) ) {
			throw new Exception('Unable to fetch user, user with `user_id` ' . (int) $this->userId . ' could not be found');
		}
		else {
			$this->initialize($userData);
		}

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

	public function getAdminAccessLevel () {

		return $this->adminAccessLevel;

	}

	public function setAcademicEmail ( $academicEmail ) {

		$this->academicEmail = $academicEmail;

	}

	public function setApproved ( $approved ) {

		$this->approved = $approved;

	}

	// Should only get called by an instance of Verify class
	public function setVerifyToken ( $verifyToken ) {

		$this->verifyToken = $verifyToken;

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

			$preparedStatement->execute([
				':google_id'      => $this->googleId,
				':google_email'   => $this->googleEmail,
				':academic_email' => $this->academicEmail,
				':verified'       => $this->verified,
				':approved'       => $this->approved,
				':token'          => $this->verifyToken,
				':user_id'        => $this->userId
			]);
		}
		catch ( PDOException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable to update user.');
		}

	}

	private function fetch ( $userId ) {

		$query = 'SELECT `user`.`user_id`, `google_id`, `google_email`, `academic_email`, `verified`, `approved`, `token`,
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

			$preparedStatement->execute([
				':user_id' => $userId
			]);

			if ( $preparedStatement->rowCount() != 0 ) {

				$res = $preparedStatement->fetch(PDO::FETCH_ASSOC);

				return [
					'googleId'         => $res['google_id'],
					'googleEmail'      => $res['google_email'],
					'academicEmail'    => $res['academic_email'],
					'admin'            => (bool) $res['is_admin'],
					'verified'         => (bool) $res['verified'],
					'approved'         => (bool) $res['approved'],
					'verifyToken'      => $res['token'],
					'adminAccessLevel' => ((bool) $res['is_admin']) === true ? (int) $res['access_level'] : null
				];

			}
			else {
				return null;
			}
		}
		catch ( PDOException $e ) {
			$logger = new ExceptionLogger();
			$logger->error($e);
			throw new Exception('Database error, unable fetch user.');
		}

	}

	private function initialize ( $userData ) {

		if ( $userData === null || empty($userData) ) {
			throw new Exception('Unable to initialize user, user data is empty');
		}

		$this->googleId = $userData['googleId'];
		$this->googleEmail = $userData['googleEmail'];
		$this->academicEmail = $userData['academicEmail'];
		$this->admin = $userData['admin'];
		$this->adminAccessLevel = $userData['adminAccessLevel'];
		$this->verified = $userData['verified'];
		$this->approved = $userData['approved'];
		$this->verifyToken = $userData['verifyToken'];

	}

}
