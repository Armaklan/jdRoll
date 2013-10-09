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
    
    public function insertNotif($user_id, $title, $content, $url) {
            $sql = "INSERT INTO notif (user_id, title, content, url) VALUES (:user, :title, :content, :url)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue("user", $user_id);
            $stmt->bindValue("title", $title);
            $stmt->bindValue("content", $content);
            $stmt->bindValue("url", $url);
            $stmt->execute();
    }
   	
	public function alertUserForMp($expediteur, $destinataires, $msgTitle, $url) {
        foreach ($destinataires as $destinaire) {
            $user = $this->userService->getByUsername($destinaire);
			$this->insertNotif($user['id'], "Nouveau message privée", "$expediteur a envoyé un mp du titre de <a href='$url'>$msgTitle</a>", $url);
		}	
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
                        $this->insertNotif($participant['id'], $title, $content, $url);
                    }
                }
                foreach($favoris as $participant) {
                    if($user_id != $participant['user_id']) {
                        $this->insertNotif($participant['user_id'], $title, $content, $url);
                    }
                }
            } else {
                $participants = $this->topicService->getWhoCanRead($topic_id);
                foreach($participants as $participant) {
                    if($user_id != $participant['user_id']) {
                        $this->insertNotif($participant['user_id'], $title, $content, $url);
                    }
                }
            }
            if($user_id != $campagne['mj_id']) {
                $this->insertNotif($campagne['mj_id'], $title, $content);
            }
            
        }
    }

}

?>
