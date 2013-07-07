<?php

class absenceService {

    private $db;
    private $session;

    public function __construct($db, $session) {
        $this->db = $db;
        $this->session = $session;
    }
    
    public function getBlankForm($user_id) {
        $absence['id'] = 0;
        $absence['begin_date'] = date("j/m/Y");
        $absence['end_date'] = date("j/m/Y");
        $absence['user_id'] = $user_id; 
        return $absence;
    }
    
    public function getForm($request) {
        $absence['id'] = $request->get('id');
        $absence['begin_date'] = $request->get('begin_date');
        $absence['end_date'] = $request->get('end_date');
        $absence['user_id'] = $request->get('user_id'); 
        return $absence;
    }
    
    public function insertAbsence($request) {
        $sql = "INSERT INTO absences 
				(user_id, begin_date, end_date) 
				VALUES
				(:user, STR_TO_DATE(:begin, '%d/%m/%Y'), STR_TO_DATE(:end, '%d/%m/%Y'))";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("user", $request->get('user_id'));
        $stmt->bindValue("begin", $request->get('begin_date'));
        $stmt->bindValue("end", $request->get('end_date'));
        $stmt->execute();
    }
    
    public function updateAbsence($request) {
        $sql = "UPDATE absences 
                SET begin_date = STR_TO_DATE(:begin, '%d/%m/%Y'),
                    end_date = STR_TO_DATE(:end, '%d/%m/%Y')
                WHERE
                    id = :id
                AND user_id = :user;";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("id", $request->get('id'));
        $stmt->bindValue("user", $request->get('user_id'));
        $stmt->bindValue("begin", $request->get('begin_date'));
        $stmt->bindValue("end", $request->get('end_date'));
        $stmt->execute();
    }
    
    public function deleteAbsence($id) {
        $sql = "DELETE FROM absences
                WHERE
                    id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("id", $id);      
        $stmt->execute();
    }
    
    public function getAbsence($id) {
        $sql = "SELECT
		id, user_id,  DATE_FORMAT(begin_date, '%d/%m/%Y') as begin_date, DATE_FORMAT(end_date, '%d/%m/%Y') as end_date
		FROM absences
		WHERE id = ?
		ORDER BY begin_date ASC";
		$absence = $this->db->fetchAssoc($sql, array($id));
		return $absence;
    }
    
    public function getAllAbsence($user_id) {
        $sql = "SELECT
		id, user_id,  DATE_FORMAT(begin_date, '%d/%m/%Y') as begin_date, DATE_FORMAT(end_date, '%d/%m/%Y') as end_date
		FROM absences
		WHERE user_id = ?
                AND end_date >= now()
		ORDER BY begin_date ASC";
		$absences = $this->db->fetchAll($sql, array($user_id));
		return $absences;
    }
    
    public function getFutureAbsenceInCampagn($id_campagne) {
        $sql = "SELECT
                user.username, DATE_FORMAT(begin_date, '%d/%m/%Y') as begin_date, DATE_FORMAT(end_date, '%d/%m/%Y') as end_date
            FROM absences
            JOIN user
            ON user.id = absences.user_id
            JOIN campagne_participant cp
            ON cp.user_id = user.id
            WHERE
            (
                (
                    absences.begin_date >= now()
                AND absences.begin_date < DATE_ADD(now(), INTERVAL 1 WEEK) 
                )
                OR (
                    absences.begin_date < now()
                AND absences.end_date >= now()
                )
            )
            AND cp.campagne_id = :campagne
            UNION
            SELECT
                user.username, DATE_FORMAT(begin_date, '%d/%m/%Y') as begin_date, DATE_FORMAT(end_date, '%d/%m/%Y') as end_date
            FROM absences
            JOIN user
            ON user.id = absences.user_id
            JOIN campagne
            ON campagne.mj_id = user.id
            WHERE
            (
                (
                    absences.begin_date >= now()
                AND absences.begin_date < DATE_ADD(now(), INTERVAL 1 WEEK) 
                )
                OR (
                    absences.begin_date < now()
                AND absences.end_date >= now()
                )
            )
            AND campagne.id = :campagne
        ";
        $absences = $this->db->fetchAll($sql, array('campagne' => $id_campagne));
	return $absences; 
    }
    
    public function getCurrentAbsence() {
        $sql = "SELECT
            user.username, user.profil
	FROM absences
        JOIN user
        ON user.id = absences.user_id
        WHERE absences.begin_date <= now()
        AND absences.end_date >= now()
        ";
        $absences = $this->db->fetchAll($sql, array());
	return $absences;    
    }

}

?>
