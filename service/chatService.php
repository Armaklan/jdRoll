<?php

class ChatService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }
    
    public function getLastMsg($id) {
		
    	$sql = "SELECT * FROM (SELECT * 
    			FROM chat WHERE id > ?
    			ORDER BY time DESC 
    			LIMIT 0, 100) chat 
    			ORDER BY time ASC";
    	return $this->db->fetchAll($sql,array($id));
    }
    
    public function postMsg($user, $text) {
        if ($text != "") {
			//On remplace le caractère '<' par son équivalent HTML
			$text = $this->escapeLowerAngleBracket($text);
			//On strip les tags HTML
            $text = strip_tags($text);
			//On remplace la forme HTML du '<' par son équivalent ascii
			$text = str_replace("&lt;", "<", $text);
            $text = $this->urllink($text);
            $text = str_replace(":)", "<img src='../../../../tinymce/plugins/emoticons/img/smiley-smile.gif' alt=''>", $text);
            $text = str_replace(";)", "<img src='../../../../tinymce/plugins/emoticons/img/smiley-wink.gif' alt=''>", $text);
            $text = str_replace(":p", "<img src='../../../../tinymce/plugins/emoticons/img/smiley-tongue-out.gif' alt=''>", $text);
            $text = str_replace(":X", "<img src='../../../../tinymce/plugins/emoticons/img/smiley-sealed.gif' alt=''>", $text);
            $text = str_replace(":'(", "<img src='../../../../tinymce/plugins/emoticons/img/smiley-cry.gif' alt=''>", $text);
            $text = str_replace("8-)", "<img src='../../../../tinymce/plugins/emoticons/img/smiley-cool.gif' alt=''>", $text);
            $text = str_replace("o-)", "<img src='../../../../tinymce/plugins/emoticons/img/smiley-innocent.gif' alt=''>", $text);
            $text = str_replace(":D", "<img src='../../../../tinymce/plugins/emoticons/img/smiley-laughing.gif' alt=''>", $text);
            $text = str_replace(":mrgreen:", "<img src='../../../../img/smileys-mrgreen.gif' alt=''>", $text);
			if(!strncasecmp($text,"/me ",4))
			{
				$text = str_ireplace("/me ",$user . " ", $text);
				$text = "<span class=\"dialogue\"><span style=\"font-size: 8.5pt; font-family: 'Verdana','sans-serif'; color: #4488cc;\">" . $text . "</span></span>";
			}
            
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
        $content = preg_replace('#(((https?://)|(w{3}\.))+[a-zA-Z0-9&;\#\.\?=_/-]+\.([a-z]{2,4})([[:alnum:]&;%,!\#\.\?=_/-]+))#i', '<a href="$0" target="_blank">$0</a>', $content);
        // Si on capte un lien tel que www.test.com, il faut rajouter le http://
        if(preg_match('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', $content)) {
            $content = preg_replace('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', '<a href="http://www.$1" target="_blank">www.$1</a>', $content);
        }

        $content = stripslashes($content);
        return $content;
    }
	
	private function escapeLowerAngleBracket($str){ 
		$find = array('/<([^[:alpha:]])/', '/<$/');
		return preg_replace($find, '&lt;\\1', $str); 
	} 
	
	public function deleteMsg($id) {
       
            
            $sql = "delete from chat
                            where id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue("id", $id);
            $stmt->execute();
			
			$sql = "INSERT INTO chat_actions( actionType, messageId ) VALUES (0,:id)";
			$stmt = $this->db->prepare($sql);
            $stmt->bindValue("id", $id);
            $stmt->execute();
        
    }
	
		public function getDeletedMessge() {
       
            
           $sql = "SELECT messageId FROM chat_actions where actionType = 0
    			ORDER BY messageId ASC";
    	return $this->db->fetchAll($sql);
        
    }
	
	public function deleteLastMsg($nb) {
       
            
			  $sql = "SELECT id from chat order by time desc limit " . $nb;
			  $rows =  $this->db->fetchAll($sql);
			  if(rows != null)
			  {
				$sql = "delete from chat order by time desc limit " . $nb;
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				$count = $stmt->rowCount();
				if($count > 0)
				{
					foreach ($rows as $msg)
					{
						$sql = "INSERT INTO chat_actions( actionType, messageId ) VALUES (0,:id)";
						$stmt = $this->db->prepare($sql);
						$stmt->bindValue("id", $msg['id']);
						$stmt->execute();
					}
				}
			}
        
    }

    
}
?>