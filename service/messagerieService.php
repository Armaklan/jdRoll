<?php
/**
 * Manage private message
 *
 * @package messagerieService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

class MessagerieService {

    private $db;
    private $session;
    private $logger;
    private $userService;
    private $mailer;

    public function __construct($db, $session, $logger, $userService, $mailer) {
        $this->db = $db;
        $this->session = $session;
        $this->logger = $logger;
        $this->userService = $userService;
        $this->mailer = $mailer;
    }

    public function getBlankMessage() {
        $perso = array();
        $perso["id"] = "";
        $perso["from_username"] = $this->session->get('user')['login'];
        $perso["from_id"] = $this->session->get('user')['id'];
        $perso["to_usernames"] = "";
        $perso["to_ids"] = "";
        $perso["title"] = "";
        $perso["content"] = "";
        return $perso;
    }

    public function getFormMessage($request) {
        $perso = array();
        $perso["id"] = $request->get('id');
        $perso["from_username"] = $request->get('from_username');
        $perso["from_id"] = $request->get('from_id');
        $perso["to_usernames"] = $request->get('to_usernames');
        $perso["to_ids"] = $request->get('to_ids');
        $perso["title"] = $request->get('title');
        $perso["content"] = $request->get('content');
        return $perso;
    }

    public function sendMessage($request) {
        $from_id =  $request->get('from_id');
        $from_username = $request->get('from_username');
        $title = $request->get('title');
        $content = $request->get('content');
        $destinataires = json_decode($request->get('to_usernames'));
        if($destinataires == null or count($destinataires) == 0) {
            throw new Exception("Veuillez indiquer un destinataire");
        }
        if(trim($title) == "") {
            throw new Exception("Le titre ne doit pas être nul");
        }
        $this->sendMessageWith($from_id, $from_username, $title, $content, $destinataires);
    }

	    public function sendMessageToMailBox($title,$content, $destinataires) {

        foreach ($destinataires as $destinaire) {
            $user = $this->userService->getByUsername($destinaire);

            try {

                $message = \Swift_Message::newInstance()
                    ->setSubject($title)
                    ->setFrom(array('contact@jdroll.org'))
                    ->setTo(array($user['mail']))
                    ->setBody($content);

                $this->mailer->send($message);
            } catch (Exception $e) {
                // Pas de mail, tant pis...
            }


        }
    }

    public function sendMessageWith($from_id, $from_username, $title, $content, $destinataires) {
        $sql = "INSERT INTO messages
    			(from_id, from_username, title, content)
    			VALUES
    			(:from_id, :from_username, :title, :content)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("from_id", $from_id);
        $stmt->bindValue("from_username", $from_username);
        $stmt->bindValue("title", $title);
        $stmt->bindValue("content", $content);
        $stmt->execute();

        $id = $this->db->lastInsertId();
        foreach ($destinataires as $destinaire) {
            $user = $this->userService->getByUsername($destinaire);
            $this->insertDestinataire($id, $user);

            try {
                $message = \Swift_Message::newInstance()
                    ->setSubject('[JdRoll] Notification de MP')
                    ->setFrom(array('contact@jdroll.org'))
                    ->setTo(array($user['mail']))
                    ->setBody("Vous avez reçu un nouveau message privée de la partie de $from_username. \n Titre : $title.");

                $this->mailer->send($message);
            } catch (Exception $e) {
                // Pas de mail, tant pis...
            }


        }
    }

    public function insertDestinataire($id, $user) {
        $sql = "INSERT INTO messages_to
    			(id_message, to_id, to_username)
    			VALUES
    			(:id_message, :to_id, :to_username)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("to_id", $user['id']);
        $stmt->bindValue("to_username", $user['username']);
        $stmt->bindValue("id_message", $id);
        $stmt->execute();
    }

    public function getReceiveMessages() {
        $sql = "SELECT messages.*, mt.statut statut_read
    			FROM messages
    			JOIN messages_to mt
    			ON messages.id = mt.id_message
    			WHERE mt.to_id = :id
    			AND mt.statut < 2
    			ORDER BY time DESC";

        return $this->db->fetchAll($sql, array('id' => $this->session->get('user')['id']));
    }

    public function getSendMessages() {
        $sql = "SELECT messages.id, messages.from_id, messages.from_username,
    			 messages.title, messages.time, GROUP_CONCAT(mt.to_username SEPARATOR ', ') as to_usernames
    			FROM messages
    			JOIN messages_to mt
    			ON messages.id = mt.id_message
    			WHERE messages.from_id = :id
    			AND messages.statut < 2
    			GROUP BY
    				messages.id, messages.from_id, messages.from_username,
    			 	messages.title, messages.time
    			ORDER BY time DESC";

        return $this->db->fetchAll($sql, array('id' => $this->session->get('user')['id']));
    }

    public function getNbNewMessages() {
        $sql = "SELECT count(messages.id)
    			FROM messages
    			JOIN messages_to mt
    			ON messages.id = mt.id_message
    			WHERE mt.to_id = :id
    			AND mt.statut = 0
    			ORDER BY time DESC";

        return $this->db->fetchColumn($sql, array('id' => $this->session->get('user')['id']), 0);
    }

    public function markRead($id) {
        $this->updateStatut($id, 1);
    }

    public function markDelete($id) {
        $this->updateStatut($id, 2);
    }

    public function markDeleteMyMsg($id) {
        $sql = "UPDATE messages
    			SET statut = :statut
    			WHERE
    			id = :id_message";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("id_message", $id);
        $stmt->bindValue("statut", 3);
        $stmt->execute();
    }

    private function updateStatut($id, $statut) {
        $sql = "UPDATE messages_to
    			SET statut = :statut
    			WHERE
    			id_message = :id_message
    			AND to_id = :to_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("to_id", $this->session->get('user')['id']);
        $stmt->bindValue("id_message", $id);
        $stmt->bindValue("statut", $statut);
        $stmt->execute();
    }

    public function getMessage($id) {
        $user = $this->session->get('user')['login'];
        $sql = "SELECT messages.id, messages.from_id, messages.from_username,
    			 messages.title, messages.time, messages.content, GROUP_CONCAT(mt.to_username SEPARATOR ', ') as to_usernames,
    			CONCAT( '\"', messages.from_username , '\",' , GROUP_CONCAT(IF(mt.to_username = :user, '', CONCAT('\"', mt.to_username, '\"')) SEPARATOR ', ')) as to_usernames_form
    			FROM messages
    			JOIN messages_to mt
    			ON messages.id = mt.id_message
    			WHERE messages.id = :id
    			GROUP BY
    				messages.id, messages.from_id, messages.from_username,
    			 	messages.title, messages.time, messages.content";

        return $this->db->fetchAssoc($sql, array('id' => $id, 'user' => $user));
    }

}

?>
