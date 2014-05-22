<?php
namespace jdRoll\service;

/**
 * Manage Campagne Information
 *
 * @package campagneService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


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

    public function getNbCampagne($statut) {
			$sql = "SELECT count(id)
					FROM campagne
                    WHERE statut = :statut";
			return $this->db->fetchColumn($sql, array('statut' => $statut), 0);
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
		$campagne['statut'] = 3;
        $campagne['rythme'] = 2;
        $campagne['rp'] = 1;
        $campagne['is_admin_open'] = 0;
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
        $campagne['rythme'] = $request->get('rythme');
        $campagne['rp'] = $request->get('rp');
		return $campagne;
    }

    public function getFormCampagneConfig($request) {
    	$campagne = array();
    	$campagne['campagne_id'] = $request->get('campagne_id');
    	$campagne['banniere'] = $request->get('banniere');
    	$campagne['hr'] = $request->get('hr');
    	$campagne['odd_line_color'] = $request->get('odd_line_color');
    	$campagne['even_line_color'] = $request->get('even_line_color');
    	$campagne['sidebar_color'] = $request->get('sidebar_color');
    	$campagne['link_color'] = $request->get('link_color');
    	$campagne['text_color'] = $request->get('text_color');
    	$campagne['default_perso_id'] = $request->get('default_perso_id');
    	$campagne['link_sidebar_color'] = $request->get('link_sidebar_color');
    	$campagne['template'] = $request->get('template');
		$campagne['template_html'] = $request->get('hiddenInput');
		$campagne['template_fields'] = $request->get('hiddenInputFields');
		$campagne['template_img'] = $request->get('imgBG');
    	$campagne['sidebar_text'] = $request->get('sidebar_text');
    	return $campagne;
    }

    public function createCampagne($request) {
		$sql = "INSERT INTO campagne
				(name, systeme, univers, nb_joueurs, description, mj_id, banniere, statut, rythme, rp)
				VALUES
				(:name,:systeme,:univers,:nb_joueurs,:description,:mj_id,:banniere, :statut, :rythme, :rp)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("name", $request->get('name'));
		$stmt->bindValue("banniere", $request->get('banniere'));
		$stmt->bindValue("systeme", $request->get('systeme'));
		$stmt->bindValue("univers", $request->get('univers'));
		$stmt->bindValue("nb_joueurs", $request->get('nb_joueurs'));
		$stmt->bindValue("description", $request->get('description'));
		$stmt->bindValue("statut", $request->get('statut'));
        $stmt->bindValue("rythme", $request->get('rythme'));
        $stmt->bindValue("rp", $request->get('rp'));
		$stmt->bindValue("mj_id", $this->session->get('user')['id']);
		$stmt->execute();

		return $this->db->lastInsertId();
    }

    public function createCampagneConfig($campagne) {
    	$sql = "INSERT INTO campagne_config
				(campagne_id, banniere, hr, odd_line_color, even_line_color, sidebar_color, link_sidebar_color, link_color, text_color, default_perso_id, template, sidebar_text)
				VALUES
				(:campagne, '', '', '', '', '', '', '', '', '', '', '')";

    	$stmt = $this->db->prepare($sql);
    	$stmt->bindValue("campagne", $campagne);
    	$stmt->execute();
    }

    public function updateCampagneConfig($request) {
    	$sql = "UPDATE campagne_config
    			SET
    			banniere = :banniere,
    			hr = :hr,
    			odd_line_color = :odd_line_color,
    			even_line_color = :even_line_color,
    			sidebar_color = :sidebar_color,
    			link_color = :link_color,
    			text_color = :text_color,
    			link_sidebar_color = :link_sidebar_color,
    			template = :template,
				template_html = :template_html,
				template_fields = :template_fields,
				template_img = :template_img,
    			sidebar_text = :sidebar_text,
    			default_perso_id = :default_perso_id
    			WHERE
    			campagne_id = :campagne";

    	$stmt = $this->db->prepare($sql);
    	$stmt->bindValue("campagne", $request->get('campagne_id'));
    	$stmt->bindValue("banniere", $request->get('banniere'));
    	$stmt->bindValue("hr", $request->get('hr'));
    	$stmt->bindValue("odd_line_color", $request->get('odd_line_color'));
    	$stmt->bindValue("even_line_color", $request->get('even_line_color'));
    	$stmt->bindValue("sidebar_color", $request->get('sidebar_color'));
    	$stmt->bindValue("link_color", $request->get('link_color'));
    	$stmt->bindValue("text_color", $request->get('text_color'));
    	$stmt->bindValue("link_sidebar_color", $request->get('link_sidebar_color'));
    	$stmt->bindValue("default_perso_id", $request->get('default_perso_id'));
    	$stmt->bindValue("template", $request->get('template'));
		$stmt->bindValue("template_html",$request->get('hiddenInput'));
		if($request->get('typeFiche') == 0)
			$stmt->bindValue("template_img",NULL);
		else
			$stmt->bindValue("template_img",$request->get('imgBG'));
		$stmt->bindValue("template_fields",$request->get('hiddenInputFields'));
    	$stmt->bindValue("sidebar_text", $request->get('sidebar_text'));
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
    				statut = :statut,
                                rythme = :rythme,
                                rp = :rp
    			WHERE
    				id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("name", $request->get('name'));
		$stmt->bindValue("banniere", $request->get('banniere'));
		$stmt->bindValue("systeme", $request->get('systeme'));
		$stmt->bindValue("univers", $request->get('univers'));
		$stmt->bindValue("statut", $request->get('statut'));
        $stmt->bindValue("rythme", $request->get('rythme'));
        $stmt->bindValue("rp", $request->get('rp'));
		$stmt->bindValue("nb_joueurs", $request->get('nb_joueurs'));
		$stmt->bindValue("description", $request->get('description'));
		$stmt->bindValue("id", $request->get('id'));
		$stmt->execute();
    }


    public function openSubscribe($id) {
    	$sql = "UPDATE campagne
    			SET is_recrutement_open = :is_recrutement_open
    			WHERE
    				id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("is_recrutement_open", 1);
		$stmt->bindValue("id", $id);
		$stmt->execute();
    }


    public function closeSubscribe($id) {
    	$sql = "UPDATE campagne
    			SET is_recrutement_open = :is_recrutement_open
    			WHERE
    				id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("is_recrutement_open", 0);
		$stmt->bindValue("id", $id);
		$stmt->execute();
    }

   	public function getAllCampagne() {
		$sql = "SELECT campagne.*, user.username as username
				FROM campagne
				JOIN user
				ON user.id = campagne.mj_id
				WHERE campagne.statut < 2
				ORDER BY campagne.statut ASC, campagne.name ASC";
	    $campagnes = $this->db->fetchAll($sql);
	    return $campagnes;
	}

        public function getArchiveCampagne() {
		$sql = "SELECT campagne.*, user.username as username
				FROM campagne
				JOIN user
				ON user.id = campagne.mj_id
				WHERE campagne.statut = 2
				ORDER BY campagne.statut ASC, campagne.name ASC";
	    $campagnes = $this->db->fetchAll($sql);
	    return $campagnes;
	}

        public function getPrepaCampagne() {
		$sql = "SELECT campagne.*, user.username as username
				FROM campagne
				JOIN user
				ON user.id = campagne.mj_id
				WHERE campagne.statut = 3
				ORDER BY campagne.statut ASC, campagne.name ASC";
	    $campagnes = $this->db->fetchAll($sql);
	    return $campagnes;
	}

	public function getLastCampagne() {
		$sql = "SELECT campagne.*, user.username as username
				FROM campagne
				JOIN user ON user.id = campagne.mj_id
				WHERE campagne.STATUT = 0
				ORDER BY campagne.id desc
				LIMIT 0, 5";
		$campagnes = $this->db->fetchAll($sql);
		return $campagnes;
	}

	public function getOpenCampagne() {
		$sql = "SELECT campagne.*, user.username as username
				FROM campagne
				JOIN user ON user.id = campagne.mj_id
				WHERE campagne.STATUT < 2
				AND is_recrutement_open = 1
				ORDER BY campagne.name ASC";
		$campagnes = $this->db->fetchAll($sql);
		return $campagnes;
	}

	public function getCampagne($id) {
        $sql = "SELECT campagne.*, user.username FROM campagne
                    JOIN user ON campagne.mj_id = user.id WHERE campagne.id = ?";
	    $campagne = $this->db->fetchAssoc($sql, array($id));
	    return $campagne;
	}

	public function getCampagneConfig($id) {
		$sql = "SELECT * FROM campagne_config WHERE campagne_id = ?";
		$campagne = $this->db->fetchAssoc($sql, array($id));
		return $campagne;
	}

	public function isMj($id) {
		if($this->session->get('user') != null) {
			if($id == null) {
				return $this->userService->getCurrentUser()['profil'] > 1;
			} else {
				$campagne = $this->getCampagne($id);
				return $campagne['mj_id'] == $this->session->get('user')['id'];
			}
		} else {
			return false;
		}
	}

    public function isAdmin() {
        return $this->isMj(0);
    }

	public function isParticipant($id) {
		if($this->session->get('user') != null) {
			$sql = "SELECT user_id
					FROM campagne_participant
					WHERE
					campagne_id = :campagne
					AND user_id = :user";
			$result = $this->db->fetchColumn($sql, array('user' => $this->session->get('user')['id'], 'campagne' => $id ), 0);
			return ($result != null);
		} else {
			return false;
		}
	}


        public function isRealJoueur($campagne_id, $user_id) {
		if($this->session->get('user') != null) {
			$sql = "SELECT user_id
					FROM campagne_participant
					WHERE
					campagne_id = :campagne
					AND user_id = :user
                                        AND statut = 1";
			$result = $this->db->fetchColumn($sql, array('user' => $user_id, 'campagne' => $campagne_id ), 0);
			return ($result != null);
		} else {
			return false;
		}
	}

	public function getMyActiveMjCampagnes() {
		return $this->getActiveMjCampagnes($this->session->get('user')['id']);
	}


	public function getActiveMjCampagnes($id) {
		return $this->getMjCampagnesByStatut(0,3, 0, $id);
	}

	public function getMyActivePjCampagnes() {
		return $this->getMyPjCampagneByStatut(0, 0, 0, 1);
	}

	public function getActivePjCampagnes($id) {
		$sql = "SELECT
		campagne.*, user.username as username, IFNULL(alert.joueur_id, 0) as campagne_alert
		FROM campagne
		JOIN campagne_participant as cp
		ON cp.campagne_id = campagne.id
		JOIN user ON user.id = campagne.mj_id
        LEFT JOIN alert
        ON
            campagne.id = alert.campagne_id
        AND cp.user_id = alert.joueur_id
		WHERE cp.user_id = ?
		AND campagne.statut = 0
		ORDER BY campagne.name";
		$campagne = $this->db->fetchAll($sql, array($id));
		return $campagne;
	}

	public function getMyCampagnes() {
        $user = $this->session->get('user')['id'];
		return $this->getMjCampagnesByStatut(0,1,3, $user);
	}

	public function getMjCampagnesByStatut($statut1, $statut2, $statut3, $user) {
		$sql = "SELECT campagne.* ,
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
				) as activity,
                IFNULL(alert.joueur_id, 0) as campagne_alert
				FROM campagne
                LEFT JOIN alert
                ON
                    campagne.id = alert.campagne_id
                AND campagne.mj_id = alert.joueur_id
				WHERE mj_id = :user
				AND campagne.statut IN (:statut1, :statut2, :statut3)
				ORDER BY name";
		$campagne = $this->db->fetchAll($sql, array('user' => $user, 'statut1' => $statut1, 'statut2' => $statut2, 'statut3' => $statut3));
		return $campagne;
	}

    public function getMyCampagnesWithWaiting() {
		$sql = "SELECT DISTINCT campagne.id, campagne.name
			FROM campagne
                        JOIN campagne_participant cp
                        ON campagne.id = campagne_id
			WHERE mj_id = :user
			AND campagne.statut <> 2
                        AND cp.statut = 0
			ORDER BY name";
		$campagne = $this->db->fetchAll($sql, array('user' => $this->session->get('user')['id']));
		return $campagne;
	}

	public function getMyPjCampagnes() {
            return $this->getMyPjCampagneByStatut(0, 1, 3, 1);
	}

    public function getMyWaitingPjCampagnes() {
            return $this->getMyPjCampagneByStatut(0, 1, 3, 0);
	}


        public function getMyPjCampagneByStatut($statut1, $statut2, $statut3, $cpstatut) {
            $sql = "SELECT distinct
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
						(topics.is_private <> 1)
						OR
						(campagne.mj_id = :user)
						OR
						(can_read.topic_id IS NOT NULL)
					)
                ) as activity,
                 IFNULL(alert.joueur_id, 0) as campagne_alert
		FROM campagne
		JOIN campagne_participant as cp
		ON cp.campagne_id = campagne.id
		JOIN user ON user.id = campagne.mj_id
        LEFT JOIN alert
        ON
            campagne.id = alert.campagne_id
        AND cp.user_id = alert.joueur_id
		WHERE cp.user_id = :user
		AND campagne.statut IN (:statut1, :statut2, :statut3)
        AND cp.statut = :cpstatut
		ORDER BY campagne.name";
		$campagne = $this->db->fetchAll($sql, array('user' => $this->session->get('user')['id'], 'statut1' => $statut1, 'statut2' => $statut2, 'statut3' => $statut3, 'cpstatut' => $cpstatut));
		return $campagne;
        }

    public function getFavorisedCampagne() {
            $sql = "SELECT distinct
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
						(topics.is_private <> 1)
						OR
						(campagne.mj_id = :user)
						OR
						(can_read.topic_id IS NOT NULL)
					)
                ) as activity,
                 IFNULL(alert.joueur_id, 0) as campagne_alert
		FROM campagne
		JOIN campagne_favoris as cp
		ON cp.campagne_id = campagne.id
		JOIN user ON user.id = campagne.mj_id
        LEFT JOIN alert
        ON
            campagne.id = alert.campagne_id
        AND cp.user_id = alert.joueur_id
		WHERE cp.user_id = :user
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
						(topics.is_private <> 1)
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
						(topics.is_private <> 1)
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
					nb_joueurs_actuel = nb_joueurs_actuel + 1,
                                        is_recrutement_open = IF(nb_joueurs_actuel = nb_joueurs, 0, 1)
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
		$sql = "SELECT user.*, cp.statut as part_statut
				FROM campagne_participant cp
				JOIN
				user
				ON user.id = cp.user_id
				WHERE
				cp.campagne_id = :campagne";
		return $this->db->fetchAll($sql, array('campagne' => $campagne_id));
	}

	public function getParticipantByStatus($campagne_id,$status) {
		$sql = "SELECT count(user_id) as nb_users
				FROM campagne_participant cp
				WHERE
				cp.campagne_id = :campagne and cp.statut = :status";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("status", $status);
		$stmt->execute();
		return $stmt->fetchColumn();
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
			throw new \Exception("Vous êtes déjà inscrit");
		}
	}

	public function addJoueur($campagne_id, $user_id) {
		$campagne = $this->getCampagne($campagne_id);
		if($campagne['nb_joueurs'] <= $campagne['nb_joueurs_actuel']) {
			throw new \Exception("La partie est déjà complète");
		}
		$this->checkIfNotParticipant($campagne_id, $user_id);
		$this->insertParticipant($campagne_id, $user_id);
		return $campagne;
	}

        public function validJoueur($campagne_id, $user_id) {
            $sql = "UPDATE campagne_participant
                    SET statut = 1
                    WHERE campagne_id = :campagne
                    AND user_id = :user";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue("campagne", $campagne_id);
            $stmt->bindValue("user", $user_id);
            $stmt->execute();

            $this->incrementeNbJoueur($campagne_id);
        }

	public function removeJoueur($campagne_id, $user_id) {
		try {
			$this->checkIfNotParticipant($campagne_id, $user_id);
			throw new \Exception("Vous n'êtes pas inscrit à cette partie.");
		} catch (\Exception $e) {

                        if($this->isRealJoueur($campagne_id, $user_id)) {
                            $this->deleteParticipant($campagne_id, $user_id);
                            $this->decrementeNbJoueur($campagne_id);
                            $this->persoService->detachPersonnage($campagne_id, $user_id);
                        } else {
                            $this->deleteParticipant($campagne_id, $user_id);
                        }
		}
	}

    public function addAlert($campagne, $joueur) {
		$sql = "INSERT INTO alert
                (campagne_id, joueur_id)
                VALUES
                (:campagne, :joueur)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne);
        $stmt->bindValue("joueur", $joueur);
		$stmt->execute();
    }

    public function removeAlert($campagne, $joueur) {

		$sql = "DELETE FROM alert
                WHERE
                campagne_id = :campagne
                AND joueur_id = :joueur ";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne);
        $stmt->bindValue("joueur", $joueur);
		$stmt->execute();
    }

    public function hasAlert($campagne, $joueur) {
        $sql = "SELECT joueur_id
                FROM alert
                WHERE
                campagne_id = :campagne
                AND joueur_id = :joueur";
        $result = $this->db->fetchColumn($sql, array('joueur' => $joueur, 'campagne' => $campagne ), 0);
        return ($result != null);
    }

    public function addFavoris($campagne, $joueur) {
		$sql = "INSERT INTO campagne_favoris
                (campagne_id, user_id)
                VALUES
                (:campagne, :joueur)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne);
        $stmt->bindValue("joueur", $joueur);
		$stmt->execute();
    }

    public function removeFavoris($campagne, $joueur) {
        $sql = "DELETE FROM campagne_favoris
                WHERE
                campagne_id = :campagne
                AND user_id = :joueur ";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne);
        $stmt->bindValue("joueur", $joueur);
		$stmt->execute();
    }

    public function isFavoris($campagne, $joueur) {
        $sql = "SELECT user_id
                FROM campagne_favoris
                WHERE
                campagne_id = :campagne
                AND user_id = :joueur";
        $result = $this->db->fetchColumn($sql, array('joueur' => $joueur, 'campagne' => $campagne ), 0);
        return ($result != null);
    }

    public function getFavorisInCampagne($campagne) {
        $sql = "SELECT user_id
                FROM campagne_favoris
                WHERE
                campagne_id = :campagne";
        return $this->db->fetchAll($sql, array('campagne' => $campagne ));
    }

    public function getNote($campagne, $user) {
        try {
            $sql = "SELECT content
                    FROM note
                    WHERE
                    campagne_id = :campagne
                    AND user_id = :user";
            return $this->db->fetchColumn($sql, array('user' => $user, 'campagne' => $campagne ), 0);
        } catch(\Exception $e) {
            return "";
        }
    }

    public function updateNote($campagne, $user, $content) {
        $sql = "UPDATE note
                SET content = :content
                , last_update = CURRENT_TIMESTAMP
                WHERE
                campagne_id = :campagne
                AND user_id = :user";
        $count = $this->db->executeUpdate($sql, array('user' => $user, 'campagne' => $campagne, 'content' => $content ));
        if($count == 0) {
            $sql = "INSERT INTO note (user_id, campagne_id, content)
                VALUES (:user, :campagne, :content)";
            $this->db->executeUpdate($sql, array('user' => $user, 'campagne' => $campagne, 'content' => $content ));
        }
    }

    public function updateIsAdminOpen($campagne, $state) {
    	$sql = "UPDATE campagne
    			SET is_admin_open = :state
    			WHERE id = :campagne";
    	$this->db->executeUpdate($sql, array('campagne' => $campagne, 'state' => $state ));
    }
}
?>
