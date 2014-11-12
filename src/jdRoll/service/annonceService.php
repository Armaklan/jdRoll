<?php
namespace jdRoll\service;


class AnnonceService {

    private $db;
    private $logger;

    public function __construct($db, $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function get() {;
        $sql = "SELECT annonce.*
                FROM annonce
                WHERE annonce.create_date < CURRENT_TIMESTAMP
                AND annonce.end_date > CURRENT_TIMESTAMP";
        return $this->db->fetchAll($sql, array());
    }

    public function all() {;
        $sql = "SELECT annonce.*
                FROM annonce
                ORDER BY create_date DESC";
        return $this->db->fetchAll($sql, array());
    }

    public function byId($id) {
        $this->logger->addInfo('By id ' . $id);
        $sql = "SELECT annonce.*
                FROM annonce
                WHERE annonce.id = ?";
        return $this->db->fetchAssoc($sql, array($id));
    }

    public function save($annonce) {
        $sql = "UPDATE annonce
                SET create_date = :begin,
                end_date = :end,
                title = :title,
                content = :content
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("begin", $annonce->create_date);
        $stmt->bindValue("end", $annonce->end_date);
        $stmt->bindValue("title", $annonce->title);
        $stmt->bindValue("content", $annonce->content);
        $stmt->bindValue("id", $annonce->id);
        $stmt->execute();
        return $this->byId($annonce->id);
    }

    public function add($annonce) {
        $sql = "INSERT INTO annonce
          (create_date, end_date, title, content)
          VALUES (
            :begin,
            :end,
            :title,
            :content
          )";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("begin", $annonce->create_date);
        $stmt->bindValue("end", $annonce->end_date);
        $stmt->bindValue("title", $annonce->title);
        $stmt->bindValue("content", $annonce->content);
        $stmt->execute();
        return $this->byId($this->db->lastInsertId());
    }

}



