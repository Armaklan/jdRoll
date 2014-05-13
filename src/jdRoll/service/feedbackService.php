<?php
namespace jdRoll\service;

class FeedbackService {

	private $db;
    private $logger;

	public function __construct($db, $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function get($id) {
        $this->logger->addInfo(' Get feedback : ' . $id );
        $sql = "SELECT feedback.* , user.username, user.id as user_id, user.avatar
                FROM feedback
                JOIN
                user
                ON feedback.user_id = user.id
				WHERE feedback.id = :id";
        $result = $this->db->fetchAssoc($sql, array("id" => $id));
    }

    public function create($user, $title, $content) {
        $this->logger->addInfo(' Create : ' . $title );
    	$sql = "INSERT INTO feedback
                (user_id, title, content)
                VALUES
                (:user, :title, :content)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("user", $user['id']);
        $stmt->bindValue("title", $title);
        $stmt->bindValue("content", $content);
        $stmt->execute();
        return $this->get($this->db->lastInsertId());
    }


}
?>
