<?php
namespace jdRoll\service\forum;

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
		$section['banniere'] = '';
		$section['default_collapse'] = '0';
		return $section;
    }

    public function getFormSection($campagne_id, $request) {
		$section = array();
		$section['id'] = $request->get('id');
		$section['campagne_id'] = $campagne_id;
		$section['title'] = $request->get('title');
		$section['ordre'] = $request->get('ordre');
		$section['banniere'] = $request->get('banniere');
		$section['default_collapse'] = $request->get('default_collapse');
		return $section;
    }

    public function getSection($section_id) {
    	$sql = "SELECT * FROM sections
				WHERE id = :section";

    	return $this->db->fetchAssoc($sql, array("section" => $section_id));
    }

    public function getNbTopicInSection($section_id) {
    	$sql = "SELECT count(*) as nb FROM topics
				WHERE section_id = :section";

    	return $this->db->fetchColumn($sql, array("section" => $section_id), 0);
    }

    public function createSection($request, $campagne_id) {
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagne", $campagne_id);
		$title = $request->get('title');
		$ordre = $request->get('ordre');
		$banniere =  $request->get('banniere');
		$default_collapse = $request->get('default_collapse');
		return $this->createSectionWith($campagne_id, $title, $ordre, $default_collapse, $banniere);
    }

    public function createSectionWith($campagne, $title, $ordre, $default_collapse, $banniere) {
    	$sql = "INSERT INTO sections
				(campagne_id, title, ordre, default_collapse, banniere)
				VALUES
				(:campagne,:title,:ordre,:default_collapse,:banniere)";

    	$stmt = $this->db->prepare($sql);
    	$stmt->bindValue("campagne", $campagne);
    	$stmt->bindValue("title", $title);
    	$stmt->bindValue("ordre", $ordre);
    	$stmt->bindValue("banniere", $banniere);
    	$stmt->bindValue("default_collapse", $default_collapse);
    	$stmt->execute();

    	return $this->db->lastInsertId();
    }


    public function updateSection($request) {
    	$sql = "UPDATE sections
    			SET title = :title,
    				ordre = :ordre,
    				default_collapse = :default_collapse,
    				banniere = :banniere
    			WHERE
    				id = :id";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("title", $request->get('title'));
		$stmt->bindValue("ordre", $request->get('ordre'));
		$stmt->bindValue("banniere", $request->get('banniere'));
		$stmt->bindValue("default_collapse", $request->get('default_collapse'));
		$stmt->bindValue("id", $request->get('id'));
		$stmt->execute();
    }

    public function deleteSection($section_id) {
    	$sql = "DELETE FROM sections WHERE id = :id";
    	$stmt = $this->db->prepare($sql);
    	$stmt->bindValue("id", $section_id);
    	$stmt->execute();
    }

    public function getSections($campagne_id) {
    	$sql = "SELECT * FROM sections WHERE campagne_id = :campagne OR (:campagne IS NULL AND campagne_id IS NULL)";
    	return $this->db->fetchAll($sql, array('campagne' => $campagne_id));
    }

   	public function getAllSectionInCampagne($campagne_id) {
   		$user_id = $this->session->get('user')['id'];
		$sql = "SELECT DISTINCT
					sections.id as section_id,
					sections.title as section_title,
					sections.banniere as section_banniere,
					sections.default_collapse as default_collapse,
					topics.id as topics_id,
					topics.title as topics_title,
					topics.stickable as topics_stickable,
					topics.is_closed as topics_is_closed,
					topics.is_private as topics_is_private,
					posts.id as posts_id,
					perso.name as posts_username,
					posts.create_date as posts_date,
					user.username as user_username,
					rd.post_id as read_post_id,
					cr.topic_id as cr_topic_id,
                    GROUP_CONCAT(accessible_perso.name SEPARATOR ',') as accessible_to
				FROM sections sections
				LEFT JOIN topics topics
				ON
					sections.id = topics.section_id
				LEFT JOIN posts posts
				ON
					posts.id = topics.last_post_id
				LEFT JOIN user user
				ON
					user.id = posts.user_id
				LEFT JOIN personnages perso
				ON
					perso.id = posts.perso_id
				LEFT JOIN read_post rd
				ON
					topics.id = rd.topic_id
				AND rd.user_id = :user
				LEFT JOIN can_read cr
				ON
					topics.id = cr.topic_id
				AND cr.user_id = :user
                LEFT JOIN can_read cr2
                ON
                    topics.id = cr2.topic_id
                LEFT JOIN personnages accessible_perso
                ON
                    accessible_perso.user_id = cr2.user_id
                and sections.campagne_id = accessible_perso.campagne_id
				WHERE
					(:campagne IS NULL and sections.campagne_id IS NULL)
					OR (sections.campagne_id = :campagne)
                GROUP BY
                    section_id,
                    section_title,
                    section_banniere,
                    default_collapse,
                    topics_id,
                    topics_title,
                    topics_stickable,
                    topics_is_closed,
                    topics_is_private,
                    posts_id,
                    posts_username,
                    posts_date,
                    user_username,
                    read_post_id,
                    cr_topic_id
				ORDER BY sections.ordre ASC, sections.title ASC, topics.stickable DESC, topics.ordre ASC, topics.title ASC";
	    $campagnes = $this->db->fetchAll($sql, array("campagne" => $campagne_id, "user" => $user_id));
	    return $campagnes;
	}

public function getQuickAllSectionInCampagne($campagne_id) {
   		$user_id = $this->session->get('user')['id'];
		$sql = "SELECT DISTINCT
					sections.id as section_id,
					sections.title as section_title,
					sections.banniere as section_banniere,
					sections.default_collapse as default_collapse,
					topics.id as topics_id,
					topics.title as topics_title,
					topics.stickable as topics_stickable,
					topics.is_closed as topics_is_closed,
					topics.is_private as topics_is_private,
                    rd.post_id as read_post_id
				FROM sections sections
				LEFT JOIN topics topics
				ON
					sections.id = topics.section_id
				LEFT JOIN read_post rd
				ON
					topics.id = rd.topic_id
				AND rd.user_id = :user
				LEFT JOIN can_read cr
				ON
					topics.id = cr.topic_id
				AND cr.user_id = :user
				WHERE
					(:campagne IS NULL and sections.campagne_id IS NULL)
					OR (sections.campagne_id = :campagne)
				ORDER BY sections.ordre ASC, sections.title ASC, topics.stickable DESC, topics.ordre ASC, topics.title ASC";
	    $campagnes = $this->db->fetchAll($sql, array("campagne" => $campagne_id, "user" => $user_id));
	    return $campagnes;
	}

	public function getLastPostInForum() {
		$sql = "SELECT DISTINCT
					sections.title as section_title,
					topics.id as topics_id,
					topics.title as topics_title,
					posts.id as posts_id,
					posts.create_date as posts_date,
					user.username as user_username
				FROM sections sections
				LEFT JOIN topics topics
				ON
					sections.id = topics.section_id
				LEFT JOIN posts posts
				ON
					posts.id = topics.last_post_id
				LEFT JOIN user user
				ON
					user.id = posts.user_id
				WHERE
					topics.is_private = 0
				AND sections.campagne_id IS NULL
				ORDER BY posts.id DESC
				LIMIT 0, 5";
		$posts = $this->db->fetchAll($sql);
		return $posts;
	}

}
?>
