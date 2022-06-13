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

    public function get($id, $user) {
        $this->logger->addInfo(' Get feedback : ' . $id );
        $sql = "SELECT feedback.* , user.username, user.avatar, user.username, user.profil, feedback_vote.id as vote_id
                FROM feedback
                JOIN
                user
                ON feedback.user_id = user.id
                LEFT JOIN feedback_vote
                ON feedback_vote.user_id = :user
                AND feedback.id = feedback_vote.id
				WHERE feedback.id = :id";
       return $this->db->fetchAssoc($sql, array("id" => $id, "user" => $user['id']));
    }

    public function getComments($id) {
        $this->logger->addInfo(' Get feedback comments : ' . $id );
        $sql = "SELECT feedback_comment.*, user.username, user.avatar, user.username, user.profil
                FROM feedback_comment
                JOIN
                user
                ON feedback_comment.user_id = user.id
				WHERE feedback_comment.feedback_id = :id";
       return $this->db->fetchAll($sql, array("id" => $id));
    }

    public function getLastFeedbacks() {
        $sql = "SELECT feedback.* , user.username, user.avatar, user.username, user.profil
                FROM feedback
                JOIN
                user
                ON feedback.user_id = user.id
				ORDER BY feedback.id DESC
                LIMIT 5";
       return $this->db->fetchAll($sql);
    }

    public function getLastComments() {
        $sql = "SELECT feedback.id, feedback.title,
                feedback.vote, feedback.content,
                feedback.user_id, feedback.create_date,
                feedback.closed, MAX(feedback_comment.id) as comment
                FROM
                feedback
                JOIN feedback_comment
                ON feedback_comment.feedback_id = feedback.id
                GROUP BY feedback.id, feedback.title, feedback.vote, feedback.content, feedback.user_id, feedback.create_date, feedback.closed
                ORDER BY comment DESC
                LIMIT 5";
       return $this->db->fetchAll($sql);
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
        return $this->get($this->db->lastInsertId(), $user);
    }

    public function createComments($feedbackId, $user, $content) {
    	$sql = "INSERT INTO feedback_comment
                (user_id,  feedback_id, content)
                VALUES
                (:user, :feedback_id, :content)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("user", $user['id']);
        $stmt->bindValue("feedback_id", $feedbackId);
        $stmt->bindValue("content", $content);
        $stmt->execute();
        return $this->get($this->db->lastInsertId(), $user);
    }

    public function delete($id, $user) {
        $this->logger->addInfo(' Delete : ' . $id );
        $sql = "UPDATE feedback
                SET closed = :closed
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->bindValue("closed", 1);
        $stmt->execute();
        return $this->get($id, $user);
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
        return $this->get($id, $user);
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
        return $this->get($id, $user);
    }

    public function getOpenFeedbacks($user) {
        $this->logger->addInfo(' Get open feedbacks');
        $sql = "SELECT feedback.* , user.username, user.id as user_id,
                user.avatar, feedback.vote, feedback_vote.id as vote_id
                FROM feedback
                JOIN
                user
                ON feedback.user_id = user.id
                LEFT JOIN feedback_vote
                ON feedback_vote.user_id = :user
                AND feedback.id = feedback_vote.id
                WHERE
                feedback.closed = :closed
                ORDER BY feedback.vote DESC
        ";
        return $this->db->fetchAll($sql, array("closed" => 0, "user" => $user['id']));
    }

    public function getStats() {
        $this->logger->addInfo(' Get stats feedbacks');
        $sql = "SELECT count(*) as total
                FROM feedback";
        $total = $this->db->fetchColumn($sql, array(),0);

        $sql = "SELECT count(*) as total
                 FROM feedback
                 WHERE closed = :closed";
        $open = $this->db->fetchColumn($sql, array("closed" => 0),0);

        $sql = "SELECT count(*) as total
                 FROM feedback_vote
                 ";
        $vote = $this->db->fetchColumn($sql, array(),0);

        return array(
            'total' => $total,
            'open' => $open,
            'vote' => $vote
        );
    }

}
?>
