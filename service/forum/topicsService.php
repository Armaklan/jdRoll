<?php

class TopicService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }
    
    public function getBlankTopic($section_id) {
		$section = array();
		$section['id'] = '';
		$section['section_id'] = $section_id;
		$section['title'] = '';
		$section['ordre'] = '';
		$section['stickable'] = '0';
		return $section;
    }

    public function getFormTopic($request) {
		$section = array();
		$section['id'] = $request->get('id');
		$section['section_id'] = $request->get('section_id');
		$section['title'] = $request->get('title');
		$section['ordre'] = $request->get('ordre');
		$section['stickable'] = $request->get('default_collapse');
		return $section;
    }

    public function getTopic($topic_id) {
    	$sql = "SELECT * FROM topics
				WHERE id = :topic";
    
    	return $this->db->fetchAssoc($sql, array("topic" => $topic_id));
    }
    
    public function createTopic($request) {	
		$sql = "INSERT INTO topics 
				(section_id, title, ordre, stickable) 
				VALUES
				(:section,:title,:ordre,:stickable)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("section", $request->get('section_id'));
		$stmt->bindValue("title", $request->get('title'));
		$stmt->bindValue("ordre", $request->get('ordre'));
		$stmt->bindValue("stickable", $request->get('stickable'));
		$stmt->execute();
    }

    public function updateTopic($request) {
    	$sql = "UPDATE topics 
    			SET title = :title,
    				ordre = :ordre,
    				stickable = :stickable
    			WHERE
    				id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("title", $request->get('title'));
		$stmt->bindValue("ordre", $request->get('ordre'));
		$stmt->bindValue("stickable", $request->get('stickable'));
		$stmt->bindValue("id", $request->get('id'));
		$stmt->execute();
    }

}
?>