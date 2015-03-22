<?php
namespace jdRoll\service;

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

    /**
     * Return carte
     * @param $id
     * @return mixed
     */
    public function getCarte($id){
        //Fetch Carte information
        $sql = "
            SELECT carte.*, mj_id
            FROM carte
            LEFT JOIN campagne ON campagne_id = campagne.id
            WHERE carte.id=?
        ";
        $result = $this->db->executeQuery($sql, array($id));
        $carte = $result->fetch(\PDO::FETCH_ASSOC);

        //Fetch PNJ
        $sql = "SELECT id, name, user_id FROM personnages WHERE campagne_id=?";
        $result = $this->db->executeQuery($sql, array($carte['campagne_id']));
        $carte['personnages'] = $result->fetchAll(\PDO::FETCH_ASSOC);
        $carte['config'] = isset($carte['config']) && $carte['config'] ? json_decode($carte['config']):new \stdClass();
        $carte['isMj'] = $carte['mj_id'] == $this->session->get('user')['id'];
        return $carte;
    }

    public function saveCarte($data){
        //List of savable fields
        $fields = array(
            'id',
            'name',
            'description',
            'image',
            'published',
            'config'
        );

        //First, we check that the user can save a map for this campaign
        $cId = $data['campagne_id'];
        $sql = "SELECT mj_id FROM campagne WHERE id=?";
        $result = $this->db->executeQuery($sql, array($cId));
        $mjId = $result->fetchColumn(0);
        if($this->session->get('user')['id'] != $mjId || ! $mjId){
            throw new \Exception("Vous n'avez pas les droits suffisants pour cette action.");
        }

        //Then, we save the data, INSERT or UPDATE is automatic
        $sql = "
            INSERT INTO carte
            (".implode(',', $fields).")
            VALUES
            (".implode(',', array_map(function($v){return ":$v";}, $fields)).")
            ON DUPLICATE KEY UPDATE
            ".implode(',', array_map(function($v){return "$v=:$v";}, $fields))."
        ";

        //Prepare SQL
        $stmt = $this->db->prepare($sql);

        //Bind the values
        foreach($data as $key=>$value){
            if(in_array($key, $fields)){
                $stmt->bindValue($key, is_array($value) ? json_encode($value):$value);
            }
        }

        //Run it!
        $stmt->execute();
    }
}