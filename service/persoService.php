<?php

class PersoService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

	private function createPersonnage($campagne_id, $user_id) {
		$sql = "INSERT INTO personnages 
				(campagne_id, user_id) 
				VALUES
				(:campagne, :user)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("user", $user_id);
		$stmt->execute();
	}

}
?>