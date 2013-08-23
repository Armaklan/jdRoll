<?php
/**
 * Manage user information and listing
 *
 * @package userService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


class UserService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

	public function login($login, $password) {
		$sql = "SELECT * FROM user WHERE username = ?";
	    $user = $this->db->fetchAssoc($sql, array($login));

	    if($user == null)  {
	    	throw new Exception('Login incorrect');
	    }
		if($user['password'] != md5($password)) {
	    	throw new Exception('Mots de passe incorrect');
	    }

	    $this->session->set('user', array('id' => $user['id'], 'login' => $login, 'avatar' => $user['avatar']));
	    $this->updateLastActionTime();
	}

	public function logout() {
		$this->session->set('user', null);
	}

	public function getCurrentUser() {
		$sessionUser = $this->session->get('user');
		if ($sessionUser == null) {
			throw new Exception('Non authentifié');
		} else {
			$login = $sessionUser['login'];
			$sql = "SELECT * FROM user WHERE username = ?";
	    	$user = $this->db->fetchAssoc($sql, array($login));
			if($user['birthDate'] != null)
				$user['birthDate'] = date("d/m/Y", strtotime($user['birthDate']));
	    	return $user;
		}
	}

	public function updateCurrentUser($request) {
		$sql = "UPDATE user
				SET
					mail = ?,
					avatar = ?,
					description = ?,
					birthDate = STR_TO_DATE(?, '%d/%m/%Y')
				WHERE username = ?";

		$this->db->executeUpdate($sql, array($request->get('mail'), $request->get('avatar'), $request->get('description'),$request->get('birthDate'), $request->get('username')));
		$userSession = $this->session->get('user');
		$userSession['avatar'] = $request->get('avatar');
		$this->session->set('user', $userSession);
		return $this->getCurrentUser();
	}

	public function updateUser($request) {
		$sql = "UPDATE user
				SET
					mail = ?,
					avatar = ?,
					description = ?,
					birthDate = STR_TO_DATE(?, '%d/%m/%Y')
				WHERE username = ?";

		$this->db->executeUpdate($sql, array($request->get('mail'), $request->get('avatar'), $request->get('description'),$request->get('birthDate') == ''?null:$request->get('birthDate'), $request->get('username')));
		return $this->getByUsername($request->get('username'));
	}

	public function subscribeUser($request) {

		if($request->get('password') != $request->get('password2')) {
			throw new Exception("Les mots de passes ne correspondent pas");
		}

		$sql = "INSERT INTO user
				(username, password, mail)
				VALUES
				(:username,:password,:mail)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("username", $request->get('username'));
		$stmt->bindValue("password", md5($request->get('password')));
		$stmt->bindValue("mail", $request->get('mail'));
		$stmt->execute();
	}

	public function changePassword($request) {

		if($request->get('password') != $request->get('password2')) {
			throw new Exception("Les mots de passes ne correspondent pas");
		}

		$sql = "UPDATE user
				SET
					password = :password
				WHERE username = :username";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("username", $this->session->get('user')['login']);
		$stmt->bindValue("password", md5($request->get('password')));
		$stmt->execute();
	}

	public function getByUsername($username) {
		$sql = "SELECT user.*, last_action.time as last_activity
                    FROM user
                    LEFT OUTER JOIN
                    last_action
                    ON
                    user.id = last_action.user_id
                    WHERE username = ?";
	    $user = $this->db->fetchAssoc($sql, array($username));
		if($user['birthDate'] != null)
				$user['birthDate'] = date("d/m/Y", strtotime($user['birthDate']));
	    return $user;
	}

        public function getById($id) {
		$sql = "SELECT * FROM user WHERE id = ?";
	    $user = $this->db->fetchAssoc($sql, array($id));
	    return $user;
	}

	public function getLastSubscribe() {
		$sql = "SELECT *
			FROM user
			ORDER BY user.id desc LIMIT 0,5";
		$users = $this->db->fetchAll($sql);
		return $users;
	}


	public function updateLastActionTime() {
		try {
			$sql = "INSERT INTO last_action
				(time, user_id)
				VALUES
				(CURRENT_TIMESTAMP, :username)";

			$stmt = $this->db->prepare($sql);
			$stmt->bindValue("username", $this->session->get('user')['id']);
			$stmt->execute();
		} catch (Exception $e) {
			$sql = "UPDATE last_action
				SET
					time = CURRENT_TIMESTAMP
				WHERE user_id = ?";

			$this->db->executeUpdate($sql, array($this->session->get('user')['id']));
		}

	}

	public function getUsernamesList() {
		$sql = "SELECT username
				FROM user";
		$usersData = $this->db->fetchAll($sql,array(),  0);
		$users = array();
		foreach($usersData as $user) {
			$users[count($users)] = $user['username'];
		}
		return implode(',', $users);
	}

        public function getAllUsers() {
		$sql = "SELECT user.*, last_action.time as last_activity
                        FROM user
                        LEFT JOIN last_action
                        ON last_action.user_id = user.id
                        WHERE user.profil >= 0
                        ORDER BY user.profil DESC, user.username ASC";

		$users = $this->db->fetchAll($sql);
		return $users;
	}

	public function getConnected() {
		$sql = "SELECT *
				FROM last_action
				JOIN user
				ON last_action.user_id = user.id
				WHERE
				time > DATE_SUB(now(), INTERVAL 5 MINUTE)
				ORDER BY user.username ASC";

		$users = $this->db->fetchAll($sql);
		return $users;
	}

	public function getConnectedIn24H() {
		$sql = "SELECT *
				FROM last_action
				JOIN user
				ON last_action.user_id = user.id
				WHERE
				time > DATE_SUB(now(), INTERVAL 24 HOUR)";

		$users = $this->db->fetchAll($sql);
		return $users;
	}

	public function getCurrentBirthDay() {
		$sql = "SELECT *
				FROM user
				WHERE MONTH( birthDate ) = MONTH( NOW( ) )
				AND DAY( birthDate ) = DAY( NOW( ) )";

		$users = $this->db->fetchAll($sql);
		return $users;
	}
}
?>
