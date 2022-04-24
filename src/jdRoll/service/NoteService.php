<?php
  namespace jdRoll\service;

/**
 *
 * @package noteService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

class NoteService {

  private $db;

  public function __construct($db) {
    $this->db = $db;
  }

  public function getNote($campagne, $user) {
    try {
      $sql = "SELECT id, content
                    FROM note
                    WHERE
                    campagne_id = :campagne
                    AND user_id = :user";
      return $this->db->fetchAll($sql, array('user' => $user, 'campagne' => $campagne ), 0);
    } catch(\Exception $e) {
      return [];
    }
  }

  public function insertNote($campagne, $user, $note) {
    $sql = "INSERT INTO note (user_id, campagne_id, content)
                VALUES (:user, :campagne, :content)";
    $this->db->executeUpdate($sql, array('user' => $user, 'campagne' => $campagne, 'content' => $note->content ));
    return $this->db->lastInsertId();
  }

  public function removeNote($campagne, $user, $note) {
    $sql = "DELETE FROM note
            WHERE
                campagne_id = :campagne
                AND user_id = :user
                AND id = :id";
    $this->db->executeUpdate($sql, array('user' => $user, 'campagne' => $campagne, 'id' => $note->id ));
  }

  public function updateNote($campagne, $user, $note) {
    if($note->id > 0 ) {
      $sql = "UPDATE note
                SET content = :content
                , last_update = CURRENT_TIMESTAMP
                WHERE
                campagne_id = :campagne
                AND user_id = :user
                AND id = :id";
      $this->db->executeUpdate($sql, array('user' => $user, 'campagne' => $campagne, 'content' => $note->content, 'id' => $note->id ));
      return $note->id;
    } else {
      return $this->insertNote($campagne, $user, $note);
    }
  }
}
