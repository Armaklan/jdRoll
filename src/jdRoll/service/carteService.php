<?php
namespace jdRoll\service;
use svay\FaceDetector;

/**
 * Manage information and listing of character
 *
 * @package persoService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

class CarteService {

    private $db;
    private $session;

    public function __construct($db, $session) {
        $this->db = $db;
        $this->session = $session;
    }

    public function getCarte($id){
        //Fetch Carte information
        $sql = "SELECT * FROM carte WHERE id=?";
        $result = $this->db->executeQuery($sql, array($id));
        $carte = $result->fetch(\PDO::FETCH_ASSOC);

        //Fetch PNJ
        $sql = "SELECT id, name FROM personnages WHERE campagne_id=?";
        $result = $this->db->executeQuery($sql, array($carte['campagne_id']));
        $carte['personnages'] = $result->fetchAll(\PDO::FETCH_ASSOC);

        return $carte;
    }
}