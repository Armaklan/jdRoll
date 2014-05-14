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
       return $this->db->fetchAssoc($sql, array("id" => $id));
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

    public function delete($id) {
        $this->logger->addInfo(' Delete : ' . $id );
        $sql = "UPDATE feedback
                SET closed = :closed
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->bindValue("closed", 1);
        $stmt->execute();
        return $this->get($id);
    }

    public function voteUp($id, $user) {
        $this->logger->addInfo(' Vote Up : ' . $id );
        $sql = "INSERT INTO feedback_vote
                (user_id, id)
                VALUES
                (:user, :id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("user", $user['id']);
        $stmt->bindValue("id", $id);
        $stmt->execute();

        $sql = "UPDATE feedback
                SET vote = vote + 1
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        return $this->get($id);
    }

    public function voteDown($id, $user) {
        $this->logger->addInfo(' Vote down : ' . $id );
        $sql = "DELETE FROM feedback_vote
                WHERE 
                user_id = :user
                AND id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("user", $user['id']);
        $stmt->bindValue("id", $id);
        $stmt->execute();

        $sql = "UPDATE feedback
                SET vote = vote - 1
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        return $this->get($id);
    }

    public function getOpenFeedbacks() {
        $this->logger->addInfo(' Get open feedbacks');
        $sql = "SELECT feedback.* , user.username, user.id as user_id, user.avatar, feedback.vote, vote.id as vote_id 
                FROM feedback
                JOIN
                user
                ON feedback.user_id = user.id
                LEFT JOIN feedback_vote vote
                ON feedback.user_id = vote.user_id
                AND feedback.id = vote.id
                WHERE
                feedback.closed = :closed
                ORDER BY id DESC
        ";
        return $this->db->fetchAll($sql, array("closed" => 0));
    }


}
?>
