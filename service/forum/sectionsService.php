<?php

class SectionService {

	private $db;
	private $session;

	public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }
    
    public function getBlankSection($campagne_id) {
		$section = array();
		$section['id'] = '';
		$section['campagne_id'] = $campagne_id;
		$section['title'] = '';
		$section['ordre'] = '';
		$section['default_collapse'] = '0';
		return $section;
    }

    public function getFormSection($campagne_id, $request) {
		$section = array();
		$section['id'] = $request->get('id');
		$section['campagne_id'] = $campagne_id;
		$section['title'] = $request->get('title');
		$section['ordre'] = $request->get('ordre');
		$section['default_collapse'] = $request->get('default_collapse');
		return $section;
    }

    public function getSection($section_id) {
    	$sql = "SELECT * FROM sections
				WHERE id = :section";
    
    	return $this->db->fetchAssoc($sql, array("section" => $section_id));
    }
    
    public function createSection($request, $campagne_id) {	
		$sql = "INSERT INTO sections 
				(campagne_id, title, ordre, default_collapse) 
				VALUES
				(:campagne,:title,:ordre,:default_collapse)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$stmt->bindValue("title", $request->get('title'));
		$stmt->bindValue("ordre", $request->get('ordre'));
		$stmt->bindValue("default_collapse", $request->get('default_collapse'));
		$stmt->execute();
    }

    public function updateSection($request) {
    	$sql = "UPDATE sections 
    			SET title = :title,
    				ordre = :ordre,
    				default_collapse = :default_collapse
    			WHERE
    				id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("title", $request->get('title'));
		$stmt->bindValue("ordre", $request->get('ordre'));
		$stmt->bindValue("default_collapse", $request->get('default_collapse'));
		$stmt->bindValue("id", $request->get('id'));
		$stmt->execute();
    }
   
    
   	public function getAllSectionInCampagne($campagne_id) {
   		$user_id = $this->session->get('user')['id'];
		$sql = "SELECT 
					sections.id as section_id,
					sections.title as section_title,
					sections.default_collapse as default_collapse,
					topics.id as topics_id,
					topics.title as topics_title,
					topics.stickable as topics_stickable,
					posts.id as posts_id,
					perso.name as posts_username,
					posts.create_date as posts_date,
					rd.post_id as read_post_id
				FROM sections sections 
				LEFT JOIN topics topics
				ON
					sections.id = topics.section_id
				LEFT JOIN posts posts
				ON
					posts.id = topics.last_post_id
				LEFT JOIN personnages perso
				ON
					perso.id = posts.perso_id
				LEFT JOIN read_post rd
				ON 
					topics.id = rd.topic_id
				WHERE 
					sections.campagne_id = :campagne
				AND ( rd.user_id = :user OR rd.user_id IS NULL)
				ORDER BY sections.ordre ASC, topics.stickable DESC, topics.ordre ASC";
	    $campagnes = $this->db->fetchAll($sql, array("campagne" => $campagne_id, "user" => $user_id));
	    return $campagnes;
	}

}
?>