<?php

class PersoService {

    private $db;
    private $session;

    public function __construct($db, $session) {
        $this->db = $db;
        $this->session = $session;
    }


    public function getBlankPnj($campagne_id) {
        $perso = array();
        $perso["name"] = "";
        $perso["avatar"] = "";
        $perso["concept"] = "";
        $perso["publicDescription"] = "";
        $perso["privateDescription"] = "";
        $perso["technical"] = "";
        $perso["statut"] = "0";
        $perso["cat_id"] = null;
        $perso["campagne_id"] = $campagne_id;
        $perso["id"] = "";
        $perso["user_id"] = null;
        return $perso;
    }

    public function createPersonnage($campagne_id, $user_id) {
        $sql = "INSERT INTO personnages 
				(name, campagne_id, user_id) 
				VALUES
				(:name, :campagne, :user)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("name", "NomPersonnage");
        $stmt->bindValue("campagne", $campagne_id);
        $stmt->bindValue("user", $user_id);
        $stmt->execute();
    }

    public function getPersonnage($createIfNotExist, $campagne_id, $user_id) {
        $sql = "SELECT * FROM personnages 
				WHERE
						campagne_id = :campagne
				AND 	user_id = :user";
        $result = $this->db->fetchAssoc($sql, array("campagne" => $campagne_id, "user" => $user_id));
        if (empty($result)) {
            if ($createIfNotExist) {
                $this->createPersonnage($campagne_id, $user_id);
                $result = $this->db->fetchAssoc($sql, array("campagne" => $campagne_id, "user" => $user_id));
            } else {
                $result['id'] = "";
            }
        }
        return $result;
    }

    public function getPersonnageById($perso_id) {
        $sql = "SELECT * FROM personnages
				WHERE
						id = :perso";
        $result = $this->db->fetchAssoc($sql, array("perso" => $perso_id));
        return $result;
    }

    public function getPersonnagesInCampagne($campagne_id) {
        $sql = "SELECT personnages.*, user.username as username FROM personnages
				JOIN user ON user.id = personnages.user_id
				WHERE
						personnages.campagne_id = :campagne
				AND 	personnages.user_id IS NOT NULL
                                ";
        $result = $this->db->fetchAll($sql, array("campagne" => $campagne_id));
        return $result;
    }

    public function getPersonnagesInCampagneLinkTopic($campagne_id, $topic_id) {
        $sql = "SELECT personnages.*, user.username as username, can_read.user_id as cr_user FROM personnages
				JOIN user ON user.id = personnages.user_id
				LEFT JOIN can_read 
				ON user.id = can_read.user_id
				AND can_read.topic_id = :topic
				WHERE
						personnages.campagne_id = :campagne
				AND 	personnages.user_id IS NOT NULL";
        $result = $this->db->fetchAll($sql, array("campagne" => $campagne_id, "topic" => $topic_id));
        return $result;
    }

    public function getPNJInCampagne($campagne_id) {
        $sql = "
            SELECT perso.* FROM 
            (   SELECT pnj.*, cat.name as cat_name, cat.id as cate_id
                FROM
                personnages pnj
                LEFT JOIN 
                pnj_category cat 
                ON pnj.cat_id = cat.id
                WHERE
                    pnj.campagne_id = :campagne
                AND 	pnj.user_id IS NULL
                AND     pnj.statut IN (0, 2)
            UNION
                SELECT pnj.*, cat.name as cat_name, cat.id as cate_id
                FROM
                personnages pnj
                RIGHT JOIN 
                pnj_category cat 
                ON pnj.cat_id = cat.id
                WHERE
                        cat.campagne_id = :campagne
            ) as perso
            ORDER BY perso.cat_name ASC, perso.name ASC";
        $result = $this->db->fetchAll($sql, array("campagne" => $campagne_id));
        return $result;
    }

    public function getAllPersonnagesInCampagne($campagne_id) {
        $sql = "SELECT * FROM personnages
				WHERE
						campagne_id = :campagne
				AND statut <> 1";
        $result = $this->db->fetchAll($sql, array("campagne" => $campagne_id));
        return $result;
    }

    public function updatePersonnage($campagne_id, $perso_id, $request) {

        $sql = "UPDATE personnages 
				SET
				name = :name,
				avatar = :avatar,
				concept = :concept,
				publicDescription = :publicDescription,
				privateDescription = :privateDescription,
				technical = :technical,
				statut = :statut,
                                cat_id = :cat_id
				WHERE
					campagne_id = :campagne
				AND id = :perso";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("campagne", $campagne_id);
        $stmt->bindValue("perso", $perso_id);
        $stmt->bindValue("name", $request->get('name'));
        $stmt->bindValue("avatar", $request->get('avatar'));
        $stmt->bindValue("concept", $request->get('concept'));
        $stmt->bindValue("publicDescription", $request->get('publicDescription'));
        $stmt->bindValue("privateDescription", $request->get('privateDescription'));
        $stmt->bindValue("technical", $request->get('technical'));
        $stmt->bindValue("statut", $request->get('statut'));
        $stmt->bindValue("cat_id", $request->get('cat_id'));
        $stmt->execute();
    }

    public function setTechnical($perso_id, $template) {

        $sql = "UPDATE personnages 
				SET
				technical = :technical
				WHERE
				id = :perso";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("perso", $perso_id);
        $stmt->bindValue("technical", $template);
        $stmt->execute();
    }

    public function insertPNJ($campagne_id, $request) {

        $sql = "INSERT INTO personnages
				(name, avatar, concept, publicDescription, privateDescription, technical, campagne_id, statut, cat_id)
				VALUES
				(:name, :avatar, :concept, :publicDescription, :privateDescription, :technical, :campagne, :statut, :cat_id)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("campagne", $campagne_id);
        $stmt->bindValue("name", $request->get('name'));
        $stmt->bindValue("avatar", $request->get('avatar'));
        $stmt->bindValue("concept", $request->get('concept'));
        $stmt->bindValue("publicDescription", $request->get('publicDescription'));
        $stmt->bindValue("privateDescription", $request->get('privateDescription'));
        $stmt->bindValue("technical", $request->get('technical'));
        $stmt->bindValue("statut", $request->get('statut'));
        $stmt->bindValue("cat_id", $request->get('cat_id'));
        $stmt->execute();
    }

    public function deletePersonnage($perso_id) {
        $perso = $this->getPersonnageById($perso_id);
        if ($perso["user_id"] != null) {
            throw new Exception("Impossible de supprimer un PNJ existant");
        }

        $sql = "UPDATE personnages 
				SET
				statut = 1
				WHERE
				id = :perso";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("perso", $perso_id);
        $stmt->execute();
    }

    public function detachPersonnage($campagne_id, $user_id) {
        $sql = "UPDATE personnages
				SET
				user_id = NULL
				WHERE
				user_id = :user
				AND campagne_id = :campagne";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("user", $user_id);
        $stmt->bindValue("campagne", $campagne_id);
        $stmt->execute();
    }

    public function attachPersonnage($campagne_id, $user_id, $perso_id) {
        $this->detachPersonnage($campagne_id, $user_id);

        $sql = "UPDATE personnages
				SET
				user_id = :user
				WHERE
				id = :perso
				AND campagne_id = :campagne";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("user", $user_id);
        $stmt->bindValue("perso", $perso_id);
        $stmt->bindValue("campagne", $campagne_id);
        $stmt->execute();
    }
    
    
    public function getBlankPnjCat($campagne_id) {
        $cat = array();
        $cat["campagne_id"] = $campagne_id;
        $cat["name"] = "";
        $cat["id"] = 0;
        return $cat;
    }

    public function getFormPnjCat() {
        $cat = array();
        $cat["campagne_id"] = $request->get('campagne_id');
        $cat["name"] = $request->get('name');
        $cat["id"] = $request->get('id');
        return $cat;
    }

    public function insertPnjCat($request) {
        $sql = "INSERT INTO pnj_category 
		(name, campagne_id) 
		VALUES
		(:name, :campagne)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("name", $request->get('name'));
        $stmt->bindValue("campagne", $request->get('campagne_id'));
        $stmt->execute();
    }
    
    public function updatePnjCat($request) {
        $sql = "UPDATE 
                    pnj_category
                SET name = :name
                WHERE
                    id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("name", $request->get('name'));
        $stmt->bindValue("id", $request->get('id'));
        $stmt->execute();
    }
    
    public function deletePnjCat($id) {
        $sql = "DELETE FROM 
                    pnj_category
                WHERE
                    id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }
    
    public function getPnjCat($id) {
        $sql = "SELECT * FROM pnj_category
		WHERE
		id = :id";
        $result = $this->db->fetchAssoc($sql, array("id" => $id));
        return $result;
    }
    
    public function getAllPnjCat($campagne_id) {
        $sql = "SELECT * FROM pnj_category
		WHERE
		campagne_id = :id";
        $result = $this->db->fetchAll($sql, array("id" => $campagne_id));
        return $result;
    }

}

?>