<?php
namespace jdRoll\service;

class ChatService {

	private $db;
	private $session;

    const TINYMCE_EMOTICONS_IMG = "../../../../vendor/tinymce/plugins/emoticons/img/";

    public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

    public function getTop10Chat() {
        $sql = "SELECT username, count(id) as cpt
                FROM chat
                WHERE username <> ''
                GROUP BY username
                ORDER BY cpt DESC
                LIMIT 0, 10
                ";
    	return $this->db->fetchAll($sql,
    			array()
    		);

    }

}
?>
