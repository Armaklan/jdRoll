<?php

class ChatService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }
    
    public function getLastMsg() {
    	$sql = "SELECT * FROM (SELECT * 
    			FROM chat
    			ORDER BY time DESC 
    			LIMIT 0, 100) chat 
    			ORDER BY time ASC";
    	return $this->db->fetchAll($sql);
    }
    
    public function postMsg($user, $text) {
        if ($text != "") {
            $text = $this->urllink($text);
            $sql = "INSERT INTO chat
                            (message, username) 
                            VALUES (:message, :user) ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue("message", $text);
            $stmt->bindValue("user", $user);
            $stmt->execute();
        }
    }
    
    private function urllink($content='') {
        $content = preg_replace('#(((https?://)|(w{3}\.))+[a-zA-Z0-9&;\#\.\?=_/-]+\.([a-z]{2,4})([a-zA-Z0-9&;\#\.\?=_/-]+))#i', '<a href="$0" target="_blank">$0</a>', $content);
        // Si on capte un lien tel que www.test.com, il faut rajouter le http://
        if(preg_match('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', $content)) {
            $content = preg_replace('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', '<a href="http://www.$1" target="_blank">www.$1</a>', $content);
        }

        $content = stripslashes($content);
        return $content;
    }

    
}
?>