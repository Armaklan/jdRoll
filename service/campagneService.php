<?php

class CampagneService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

    public function getBlankCampagne() {
		$campagne = array();
		$campagne['id'] = '';
		$campagne['nb_joueurs'] = '4';
		$campagne['name'] = '';
		$campagne['systeme'] = '';
		$campagne['univers'] = '';
		$campagne['description'] = '';
		return $campagne;
    }

    public function getFormCampagne($request) {
		$campagne = array();
		$campagne['id'] = $request->get('id');
		$campagne['nb_joueurs'] = $request->get('nb_joueurs');
		$campagne['name'] = $request->get('name');
		$campagne['systeme'] = $request->get('systeme');
		$campagne['univers'] = $request->get('univers');
		$campagne['description'] = $request->get('description');
		return $campagne;
    }

    public function createCampagne($request) {
    	
		$sql = "INSERT INTO campagne 
				(name, systeme, univers, nb_joueurs, description, mj_id) 
				VALUES
				(:name,:systeme,:univers,:nb_joueurs,:description,:mj_id)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("name", $request->get('name'));
		$stmt->bindValue("systeme", $request->get('systeme'));
		$stmt->bindValue("univers", $request->get('univers'));
		$stmt->bindValue("nb_joueurs", $request->get('nb_joueurs'));
		$stmt->bindValue("description", $request->get('description'));
		$stmt->bindValue("mj_id", $this->session->get('user')['id']);
		$stmt->execute();
    }

    public function updateCampagne($request) {
    	$sql = "UPDATE campagne 
    			SET name = :name,
    				systeme = :systeme,
    				univers = :univers,
    				nb_joueurs = :nb_joueurs,
    				description = :description
    			WHERE
    				id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("name", $request->get('name'));
		$stmt->bindValue("systeme", $request->get('systeme'));
		$stmt->bindValue("univers", $request->get('univers'));
		$stmt->bindValue("nb_joueurs", $request->get('nb_joueurs'));
		$stmt->bindValue("description", $request->get('description'));
		$stmt->bindValue("id", $request->get('id'));
		$stmt->execute();
    }

   	public function getAllCampagne() {
		$sql = "SELECT campagne.*, user.username as username
				FROM campagne 
				JOIN user ON user.id = campagne.mj_id ORDER BY id desc";
	    $campagnes = $this->db->fetchAll($sql);
	    return $campagnes;
	}

	public function getLastCampagne() {
		$sql = "SELECT campagne.*, user.username as username
				FROM campagne 
				JOIN user ON user.id = campagne.mj_id
				ORDER BY campagne.id desc TOP 5";
	    $campagnes = $this->db->fetchAll($sql);
	    return $campagnes;
	}

	public function getCampagne($id) {
		$sql = "SELECT * FROM campagne WHERE id = " . $id;
	    $campagne = $this->db->fetchAssoc($sql);
	    return $campagne;
	}

	public function getMyActiveCampagnes() {
		$sql = "SELECT * FROM campagne WHERE mj_id = ? ORDER BY name";
	    $campagne = $this->db->fetchAll($sql, array($this->session->get('user')['id']));
	    return $campagne;
	}
}
?>