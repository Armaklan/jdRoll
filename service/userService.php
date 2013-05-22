<?php

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
	    	return $user;
		}
	}

	public function updateCurrentUser($request) {
		$sql = "UPDATE user 
				SET 
					mail = ?,
					avatar = ?,
					description = ?
				WHERE username = ?";

		$this->db->executeUpdate($sql, array($request->get('mail'), $request->get('avatar'), $request->get('description'), $request->get('username')));
		$userSession = $this->session->get('user');
		$userSession['avatar'] = $request->get('avatar');
		$this->session->set('user', $userSession);
		return $this->getCurrentUser();
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
		$sql = "SELECT * FROM user WHERE username = ?";
	    $user = $this->db->fetchAssoc($sql, array($username));
	    return $user;
	}
	
	public function getLastSubscribe() {
		$sql = "SELECT *
			FROM user
			ORDER BY user.id desc LIMIT 0,5";
		$users = $this->db->fetchAll($sql);
		return $users;
	}
}
?>