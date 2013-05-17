<?php

class PersoService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

	public function createPersonnage($campagne_id, $user_id) {
		$sql = "INSERT INTO personnages 
				(campagne_id, user_id) 
				VALUES
				(:campagne, :user)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("user", $user_id);
		$stmt->execute();
	}


	public function getPersonnage($campagne_id, $user_id) {
		$sql = "SELECT * FROM personnages 
				WHERE
						campagne_id = :campagne
				AND 	user_id = :user";
		$result = $this->db->fetchAssoc($sql, array("campagne" => $campagne_id, "user" => $user_id));
		if(empty($result)) {
			$this->createPersonnage($campagne_id, $user_id);
			$result = $this->db->fetchAssoc($sql, array("campagne" => $campagne_id, "user" => $user_id));
		}
		return $result;
	}

	public function updatePersonnage($campagne_id, $user_id, $request) {

		$sql = "UPDATE personnages 
				SET
				name = :name,
				avatar = :avatar,
				publicDescription = :publicDescription,
				privateDescription = :privateDescription,
				technical = :technical
				WHERE
					campagne_id = :campagne
				AND user_id = :user";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("user", $user_id);
		$stmt->bindValue("name", $request->get('name'));
		$stmt->bindValue("avatar", $request->get('avatar'));
		$stmt->bindValue("publicDescription",  $request->get('publicDescription'));
		$stmt->bindValue("privateDescription",  $request->get('privateDescription'));
		$stmt->bindValue("technical",  $request->get('technical'));
		$stmt->execute();
	}

}
?>