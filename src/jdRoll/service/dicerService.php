<?php
namespace jdRoll\service;

class Jet {

	public $type;
	public $result;

	public function __construct($type, $result)
	{
		$this->type = $type;
		$this->result = $result;
	}

	public function toStr() {
		if($this->result != "") {
			return " D" . $this->type . "(" . $this->result . ")";
		} else {
			return " +" . $this->type;
		}
	}
}

class DicerService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

	public function launchDice($campagne_id, $param, $description) {
		$result = "";
		if($param == '') {
			throw new \Exception("Aucun jet demandé");
		}


        $dicer = new \Dicer();
        $result = $dicer->parse($param);

		$this->insertDice($campagne_id, $result, $description);
		return $result;
	}

	public function getAllDice($campagneId) {
		$sql = "SELECT dicer.*, user.username as username
				FROM dicer
				JOIN user
				ON
					dicer.user_id = user.id
				WHERE campagne_id = ?
				ORDER BY id DESC
				LIMIT 0, 30";
		$campagne = $this->db->fetchAll($sql, array($campagneId));
		return $campagne;
	}

	public function getUserDice($campagneId, $userId) {
		$sql = "SELECT dicer.*, user.username as username
				FROM dicer
				JOIN user
				ON
					dicer.user_id = user.id
				WHERE campagne_id = ?
                AND user.id = ?
				ORDER BY id DESC
				LIMIT 0, 30";
		$campagne = $this->db->fetchAll($sql, array($campagneId, $userId));
		return $campagne;
	}

	/**
	 * @param string $result
	 */
	public function insertDice($campagne_id, $result, $description) {
		$sql = "INSERT INTO dicer
				(user_id, campagne_id, result, description)
				VALUES
				(:user_id,:campagne_id,:result, :description)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("result", $result);
		$stmt->bindValue("campagne_id", $campagne_id);
		$stmt->bindValue("description", $description);
		$stmt->bindValue("user_id", $this->session->get('user')['id']);
		$stmt->execute();
	}


}
?>
