<?php
/**
 * Notification Center
 *
 * @package notificationService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

class NotificationService {

    private $db;
    private $logger;
    private $topicService;
    private $campagneService;
    private $userService;

    public function __construct($db, $logger, $userService, $topicService, $campagneService) {
        $this->db = $db;
        $this->logger = $logger;
        $this->userService = $userService;
        $this->topicService = $topicService;
        $this->campagneService = $campagneService;
    }
    
    public function getNotifForUser($user_id) {
        $sql = "SELECT *
                FROM notif
                WHERE user_id = :user
                ORDER BY id DESC;";
        return $this->db->fetchAll($sql, array('user' => $user_id));
    }
    
    public function deleteNotif($id) {
            $sql = "DELETE FROM notif WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue("id", $id);
            $stmt->execute();
    }
    
    public function insertNotif($user_id, $title, $content, $url, $type, $target_id) {
        $nbNotif = 0;

        if($type == "MSG") {
            $sql = "SELECT count(*) as nb 
            FROM notif 
            WHERE 
                user_id = :user
            AND type = :type
            AND target_id = :target_id";

            $nbNotif = $this->db->fetchColumn($sql, array('user' => $user_id, 'type' => $type, 'target_id' => $target_id), 0);
        }

        if( $nbNotif == 0 ) {
            $sql = "INSERT INTO notif (user_id, title, content, url, type, target_id) VALUES (:user, :title, :content, :url, :type, :target_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue("user", $user_id);
            $stmt->bindValue("title", $title);
            $stmt->bindValue("content", $content);
            $stmt->bindValue("url", $url);
            $stmt->bindValue("type", $type);
            $stmt->bindValue("target_id", $target_id);
            $stmt->execute();
        } else {
            $sql = "UPDATE notif 
                    SET content = CONCAT(content, ' (Plusieurs messages non lues) ')
                    WHERE 
                        user_id = :user
                    AND type = :type
                    AND target_id = :target_id
                    AND content NOT LIKE '%Plusieurs messages non lues%' ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue("user", $user_id);
            $stmt->bindValue("type", $type);
            $stmt->bindValue("target_id", $target_id);
            $stmt->execute();
        }
    }
   	
	public function alertUserForMp($expediteur, $destinataires, $msgTitle, $url) {
        foreach ($destinataires as $destinaire) {
            $user = $this->userService->getByUsername($destinaire);
			$this->insertNotif($user['id'], "Nouveau message privée", "$expediteur a envoyé un mp du titre de <a href='$url'>$msgTitle</a>", $url, 'MP', 0);
		}	
	}	

	public function alertModifPerso($user_id, $perso, $campagne_id, $urlPj, $urlMj) {
		$persoUser = $perso['user_id'];
        $campagne = $this->campagneService->getCampagne($campagne_id);
		$mj = $campagne['mj_id'];
		if($mj != $user_id) {
			$this->insertNotif($mj, "Modification de personnage - " . $campagne['name'], "Le personnage <a href='$urlMj'>"
			   	. $perso['name'] . "</a> a été modifié.", $urlMj, 'PERSO', 0);
		}
		if($persoUser != $user_id) {
			$this->insertNotif($persoUser, "Modification de personnage - " . $campagne['name'],
			   	"Le personnage <a href='$urlPj'>" . $perso['name'] . "</a> a été modifié par le maître de jeu.", $urlPj, 'PERSO', 0);
		}
	}

	public function alertJoinCampagne($campagne, $joueur, $url) {
		$user = $campagne['mj_id'];
		$title = "Nouvelle inscription - " . $campagne['name'];
		$content = "$joueur s'est inscrit sur <a href='$url'>la partie.</a>"; 
		$this->insertNotif($user, $title, $content, $url, 'JOIN', 0);
	}

	public function alertQuitCampagne($campagne, $joueur, $url) {
		$user = $campagne['mj_id'];
		$title = "Désinscription - " . $campagne['name'];
		$content = "$joueur s'est désinscrit sur <a href='$url'>la partie.</a>"; 
		$this->insertNotif($user, $title, $content, $url, 'QUIT', 0);
	}

    public function alertPostInCampagne($user_id, $campagne_id, $topic_id, $url) {
        if($campagne_id != 0) {
            $user = $this->userService->getById($user_id);
            $campagne = $this->campagneService->getCampagne($campagne_id);
            $participants = $this->campagneService->getParticipant($campagne_id);
            $favoris = $this->campagneService->getFavorisInCampagne($campagne_id);
            $topic = $this->topicService->getTopic($topic_id);
            $content = "Nouveau message de " . $user['username'] . " dans le sujet <a href='$url'>" . $topic['title'] . "</a>";
            $title = "Nouveau message - " . $campagne['name'];
            if($topic['is_private'] != 1) {
                // FIXIT Arma - Capitalisation
                foreach($participants as $participant) {
                    if($user_id != $participant['id']) {
                        $this->insertNotif($participant['id'], $title, $content, $url, 'MSG', $topic_id);
                    }
                }
                foreach($favoris as $participant) {
                    if($user_id != $participant['user_id']) {
                        $this->insertNotif($participant['user_id'], $title, $content, $url, 'MSG', $topic_id);
                    }
                }
            } else {
                $participants = $this->topicService->getWhoCanRead($topic_id);
                foreach($participants as $participant) {
                    if($user_id != $participant['user_id']) {
                        $this->insertNotif($participant['user_id'], $title, $content, $url, 'MSG', $topic_id);
                    }
                }
            }
            if($user_id != $campagne['mj_id']) {
                $this->insertNotif($campagne['mj_id'], $title, $content, 'MSG', $topic_id);
            }
            
        }
    }

}

?>
