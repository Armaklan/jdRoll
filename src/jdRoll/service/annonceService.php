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

}

