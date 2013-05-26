<?php

class CampagneService {

	private $db;
	private $session;
	private $persoService;
	private $userService;

	public function __construct($db, $session,$persoService, $userService)
    {
        $this->db = $db;
        $this->session = $session;
        $this->persoService = $persoService;
        $this->userService = $userService;
    }

    public function getBlankCampagne() {
		$campagne = array();
		$campagne['id'] = '';
		$campagne['nb_joueurs'] = '4';
		$campagne['name'] = '';
		$campagne['banniere'] = '';
		$campagne['systeme'] = '';
		$campagne['univers'] = '';
		$campagne['description'] = '';
		$campagne['statut'] = 0;
		return $campagne;
    }

    public function getFormCampagne($request) {
		$campagne = array();
		$campagne['id'] = $request->get('id');
		$campagne['nb_joueurs'] = $request->get('nb_joueurs');
		$campagne['name'] = $request->get('name');
		$campagne['banniere'] = $request->get('banniere');
		$campagne['systeme'] = $request->get('systeme');
		$campagne['univers'] = $request->get('univers');
		$campagne['description'] = $request->get('description');
		$campagne['statut'] = $request->get('statut');
		return $campagne;
    }

    public function createCampagne($request) {	
		$sql = "INSERT INTO campagne 
				(name, systeme, univers, nb_joueurs, description, mj_id, banniere, statut) 
				VALUES
				(:name,:systeme,:univers,:nb_joueurs,:description,:mj_id,:banniere, :statut)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("name", $request->get('name'));
		$stmt->bindValue("banniere", $request->get('banniere'));
		$stmt->bindValue("systeme", $request->get('systeme'));
		$stmt->bindValue("univers", $request->get('univers'));
		$stmt->bindValue("nb_joueurs", $request->get('nb_joueurs'));
		$stmt->bindValue("description", $request->get('description'));
		$stmt->bindValue("statut", $request->get('statut'));
		$stmt->bindValue("mj_id", $this->session->get('user')['id']);
		$stmt->execute();
    }

    public function updateCampagne($request) {
    	$sql = "UPDATE campagne 
    			SET name = :name,
    				banniere = :banniere,
    				systeme = :systeme,
    				univers = :univers,
    				nb_joueurs = :nb_joueurs,
    				description = :description,
    				statut = :statut
    			WHERE
    				id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("name", $request->get('name'));
		$stmt->bindValue("banniere", $request->get('banniere'));
		$stmt->bindValue("systeme", $request->get('systeme'));
		$stmt->bindValue("univers", $request->get('univers'));
		$stmt->bindValue("statut", $request->get('statut'));
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
	
	public function getOpenCampagne() {
		$sql = "SELECT campagne.*, user.username as username
				FROM campagne
				JOIN user ON user.id = campagne.mj_id
				WHERE STATUT < 2
				AND nb_joueurs_actuel < nb_joueurs
				ORDER BY id desc";
		$campagnes = $this->db->fetchAll($sql);
		return $campagnes;
	}

	public function getLastCampagne() {
		$sql = "SELECT campagne.*
				FROM campagne 
				WHERE STATUT = 0
				ORDER BY campagne.id desc 
				LIMIT 0, 5";
	    $campagnes = $this->db->fetchAll($sql);
	    return $campagnes;
	}

	public function getCampagne($id) {
		$sql = "SELECT * FROM campagne WHERE id = ?";
	    $campagne = $this->db->fetchAssoc($sql, array($id));
	    return $campagne;
	}
	
	public function getCampagneConfig($id) {
		$sql = "SELECT * FROM campagne_config WHERE campagne_id = ?";
		$campagne = $this->db->fetchAssoc($sql, array($id));
		return $campagne;
	}
	
	public function isMj($id) {
		if($id == null) {
			return $this->userService->getCurrentUser()['profil'] > 0;
		} else {
			$campagne = $this->getCampagne($id);
			return $campagne['mj_id'] == $this->session->get('user')['id'];
		}
	}

	public function getMyActiveMjCampagnes() {
		$sql = "SELECT *
				FROM campagne
				WHERE mj_id = :user 
				AND statut = 0
				ORDER BY name";
	    $campagne = $this->db->fetchAll($sql, array('user' => $this->session->get('user')['id']));
	    return $campagne;
	}

	public function getMyActivePjCampagnes() {
		$sql = "SELECT 
		campagne.*, user.username as username
		FROM campagne
		JOIN campagne_participant as cp
		ON cp.campagne_id = campagne.id
		JOIN user ON user.id = campagne.mj_id
		WHERE cp.user_id = ? 
		AND statut = 0
		ORDER BY campagne.name";
	    $campagne = $this->db->fetchAll($sql, array($this->session->get('user')['id']));
	    return $campagne;
	}
	
	public function getMyCampagnes() {
		$sql = "SELECT * ,
				( SELECT
					max((IFNULL(topics.last_post_id, 0) - IFNULL(read_post.post_id, 0)))
					FROM
					sections
					JOIN topics
					ON sections.id = topics.section_id
					LEFT JOIN read_post
					ON read_post.topic_id = topics.id
					AND read_post.user_id = :user
					WHERE
					sections.campagne_id = campagne.id
				) as activity
				FROM campagne
				WHERE mj_id = :user
				AND statut < 2
				ORDER BY name";
		$campagne = $this->db->fetchAll($sql, array('user' => $this->session->get('user')['id']));
		return $campagne;
	}
	
	public function getMyPjCampagnes() {
		$sql = "SELECT
		campagne.*, user.username as username,
				( SELECT 
					max((IFNULL(topics.last_post_id, 0) - IFNULL(read_post.post_id, 0)))
					FROM 
					sections
					JOIN topics
					ON sections.id = topics.section_id
					LEFT JOIN read_post
					ON read_post.topic_id = topics.id
					AND read_post.user_id = :user
					LEFT JOIN can_read
					ON can_read.topic_id = topics.id
					AND can_read.user_id = :user
					WHERE
					sections.campagne_id = campagne.id 
					AND (
						(topics.is_private = 0)
						OR
						(campagne.mj_id = :user)
						OR
						(can_read.topic_id IS NOT NULL)
					)
				) as activity			
		FROM campagne
		JOIN campagne_participant as cp
		ON cp.campagne_id = campagne.id
		JOIN user ON user.id = campagne.mj_id
		WHERE cp.user_id = :user
		AND statut < 2
		ORDER BY campagne.name";
		$campagne = $this->db->fetchAll($sql, array('user' => $this->session->get('user')['id']));
		return $campagne;
	}

	public function getMyMjArchiveCampagnes() {
		$sql = "SELECT * ,
				( SELECT
					max((IFNULL(topics.last_post_id, 0) - IFNULL(read_post.post_id, 0)))
					FROM
					sections
					JOIN topics
					ON sections.id = topics.section_id
					LEFT JOIN read_post
					ON read_post.topic_id = topics.id
					AND read_post.user_id = :user
					LEFT JOIN can_read
					ON can_read.topic_id = topics.id
					AND can_read.user_id = :user
					WHERE
					sections.campagne_id = campagne.id
					AND (
						(topics.is_private = 0)
						OR
						(campagne.mj_id = :user)
						OR
						(can_read.topic_id IS NOT NULL)
					)
				) as activity
				FROM campagne
				WHERE mj_id = :user
				AND statut = 2
				ORDER BY name";
		$campagne = $this->db->fetchAll($sql, array('user' => $this->session->get('user')['id']));
		return $campagne;
	}
	
	public function getMyPjArchiveCampagnes() {
		$sql = "SELECT
		campagne.*, user.username as username,
				( SELECT
					max((IFNULL(topics.last_post_id, 0) - IFNULL(read_post.post_id, 0)))
					FROM
					sections
					JOIN topics
					ON sections.id = topics.section_id
					LEFT JOIN read_post
					ON read_post.topic_id = topics.id
					AND read_post.user_id = :user
					LEFT JOIN can_read
					ON can_read.topic_id = topics.id
					AND can_read.user_id = :user
					WHERE
					sections.campagne_id = campagne.id
					AND (
						(topics.is_private = 0)
						OR
						(campagne.mj_id = :user)
						OR
						(can_read.topic_id IS NOT NULL)
					)
				) as activity
		FROM campagne
		JOIN campagne_participant as cp
		ON cp.campagne_id = campagne.id
		JOIN user ON user.id = campagne.mj_id
		WHERE cp.user_id = :user
		AND campagne.statut = 2
		ORDER BY campagne.name";
		$campagne = $this->db->fetchAll($sql, array('user' => $this->session->get('user')['id']));
		return $campagne;
	}
	
	private function incrementeNbJoueur($id) {
		$sql = "UPDATE campagne 
				SET 
					nb_joueurs_actuel = nb_joueurs_actuel + 1
				WHERE id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("id", $id);
		$stmt->execute();
	}

	private function decrementeNbJoueur($id) {
		$sql = "UPDATE campagne 
				SET 
					nb_joueurs_actuel = nb_joueurs_actuel - 1
				WHERE id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("id", $id);
		$stmt->execute();
	}
	
	public function getParticipant($campagne_id) {
		$sql = "SELECT user.* 
				FROM campagne_participant cp
				JOIN
				user
				ON user.id = cp.user_id
				WHERE
				cp.campagne_id = :campagne";
		return $this->db->fetchAll($sql, array('campagne' => $campagne_id));
	}

	private function insertParticipant($campagne_id, $user_id) {
		$sql = "INSERT INTO campagne_participant 
				(campagne_id, user_id) 
				VALUES
				(:campagne, :user)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("user", $user_id);
		$stmt->execute();
	}

	private function deleteParticipant($campagne_id, $user_id) {
		$sql = "DELETE FROM campagne_participant 
				WHERE
				campagne_id = :campagne
				AND user_id = :user";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("user", $user_id);
		$stmt->execute();
	}

	private function createPersonnage($campagne_id, $user_id) {
		$sql = "INSERT INTO campagne_participant 
				(campagne_id, user_id) 
				VALUES
				(:campagne, :user)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("user", $user_id);
		$stmt->execute();
	}

	private function checkIfNotParticipant($campagne_id, $user_id) {
		$sql = "SELECT count(*) FROM campagne_participant 
				WHERE campagne_id = :campagne
				AND   user_id = :user";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("user", $user_id);
		$stmt->execute();

		$res = $stmt->fetchColumn(0);
		if($res > 0) {
			throw new Exception("Vous êtes déjà inscrit");
		}
	}

	public function addJoueur($campagne_id, $user_id) {
		$campagne = $this->getCampagne($campagne_id);
		if($campagne['nb_joueurs'] <= $campagne['nb_joueurs_actuel']) {
			throw new Exception("La partie est déjà complète");
		}
		$this->checkIfNotParticipant($campagne_id, $user_id);
		$this->incrementeNbJoueur($campagne_id);
		$this->insertParticipant($campagne_id, $user_id);
	}

	public function removeJoueur($campagne_id, $user_id) {
		try {
			$this->checkIfNotParticipant($campagne_id, $user_id);
			throw new Exception("Vous n'êtes pas inscrit à cette partie.");
		} catch (Exception $e) {
			$this->decrementeNbJoueur($campagne_id);
			$this->deleteParticipant($campagne_id, $user_id);
			$this->persoService->detachPersonnage($campagne_id, $user_id);
		}	
	}
}
?>