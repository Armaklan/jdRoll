<?php

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

	public function launchDice($campagne_id, $param) {
		$result = "";
		if($param == '') {
			throw new Exception("Aucun jet demandé");
		}
		$param = mb_strtolower($param);
		$dices = explode("+", $param);
		$jets = array();
		$sommeJet = 0;
		foreach ($dices as $dice) {
			$diceElt = explode("d", $dice);
			if(count($diceElt) > 2) {
				throw new Exception("Les dés doivent être exprimées sous la forme 1d6");
			}
			if(count($diceElt) == 2) {
				$numberDice = trim($diceElt[0]);
				$typeDice = trim($diceElt[1]);
				
				for ($i = 0; $i < $numberDice; $i++) {
					$jet = rand(1,$typeDice);
					$sommeJet = $sommeJet + $jet;
					$jets[count($jets)] = new Jet($typeDice, $jet);
				}
			} else {
				$typeDice = trim($diceElt[0]);
				$jets[count($jets)] = new Jet($typeDice , "");
				$sommeJet = $sommeJet + $typeDice;
			}
		}

		foreach($jets as $jet) {
			$result = $result . " " . $jet->toStr();
		}
		$result = $result . " = " . $sommeJet;
		$this->insertDice($campagne_id, $result);
		return $result;
	}
	
	public function getDice($campagne_id) {
		$sql = "SELECT dicer.*, user.username as username
				FROM dicer 
				JOIN user
				ON
					dicer.user_id = user.id
				WHERE campagne_id = ? 
				ORDER BY id DESC
				LIMIT 0, 30";
		$campagne = $this->db->fetchAll($sql, array($campagne_id));
		return $campagne;
	}
	
	public function insertDice($campagne_id, $result) {
		$sql = "INSERT INTO dicer
				(user_id, campagne_id, result)
				VALUES
				(:user_id,:campagne_id,:result)";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("result", $result);
		$stmt->bindValue("campagne_id", $campagne_id);
		$stmt->bindValue("user_id", $this->session->get('user')['id']);
		$stmt->execute();
	}
	

}
?>