<?php

class MessagerieService {

	private $db;
	private $session;
	private $logger;
	private $userService;

	public function __construct($db, $session, $logger, $userService)
    {
        $this->db = $db;
        $this->session = $session;
        $this->logger = $logger;
        $this->userService = $userService;
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
    	$sql = "INSERT INTO messages 
    			(from_id, from_username, title, content)
    			VALUES
    			(:from_id, :from_username, :title, :content)";
    	
    	$stmt = $this->db->prepare($sql);
    	$stmt->bindValue("from_id", $request->get('from_id'));
    	$stmt->bindValue("from_username", $request->get('from_username'));
    	$stmt->bindValue("title", $request->get('title'));
    	$stmt->bindValue("content", $request->get('content'));
    	$stmt->execute();
    	
    	$id = $this->db->lastInsertId();
    	$destinataires = json_decode($request->get('to_usernames'));
    	foreach ($destinataires as $destinaire) {
    		$user = $this->userService->getByUsername($destinaire);
    		$this->insertDestinataire($id, $user);
    	}
    	$this->logger->addInfo("To : " . $destinaire);
    	
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
    	$sql = "SELECT messages.*
    			FROM messages
    			JOIN messages_to mt
    			ON messages.id = mt.id_message
    			WHERE mt.to_id = :id
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
    			GROUP BY
    				messages.id, messages.from_id, messages.from_username,
    			 	messages.title, messages.time
    			ORDER BY time DESC";
    	 
    	return $this->db->fetchAll($sql, array('id' => $this->session->get('user')['id']));
    }
    
    public function getMessage($id) {
    	$sql = "SELECT messages.id, messages.from_id, messages.from_username,
    			 messages.title, messages.time, messages.content, GROUP_CONCAT(mt.to_username SEPARATOR ', ') as to_usernames,
    			GROUP_CONCAT(CONCAT('\"', mt.to_username, '\"') SEPARATOR ', ') as to_usernames_form
    			FROM messages
    			JOIN messages_to mt
    			ON messages.id = mt.id_message
    			WHERE messages.id = :id
    			GROUP BY
    				messages.id, messages.from_id, messages.from_username,
    			 	messages.title, messages.time, messages.content";
    
    	return $this->db->fetchAssoc($sql, array('id' => $id));
    }

}
?>