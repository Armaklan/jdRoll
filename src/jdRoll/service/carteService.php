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

    public function __construct($db, $session, $thumbnailService) {
        $this->db = $db;
        $this->session = $session;
        $this->thumbnailService = $thumbnailService;
    }

    protected function _mustBeMj($campagneId){
        $sql = "SELECT mj_id FROM campagne WHERE id=?";
        $result = $this->db->executeQuery($sql, array($campagneId));
        $mjId = $result->fetchColumn(0);
        if($this->session->get('user')['id'] != $mjId || ! $mjId){
            throw new \Exception("Vous n'avez pas les droits suffisants pour cette action.");
        }
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

        if( ! $carte['published']){
            $this->_mustBeMj($carte['campagne_id']);
        }

        //Fetch PNJ
        $sql = "SELECT id, name, user_id FROM personnages WHERE campagne_id=?";
        $result = $this->db->executeQuery($sql, array($carte['campagne_id']));
        $carte['personnages'] = $result->fetchAll(\PDO::FETCH_ASSOC);
        $carte['config'] = isset($carte['config']) && $carte['config'] ? json_decode($carte['config']):new \stdClass();
        $carte['isMj'] = isset($carte['mj_id']) && $carte['mj_id'] == $this->session->get('user')['id'];
        return $carte;
    }

    /**
     * Return all cartes from a campagne
     * @param $campagne_id
     * @param $withUnpublished
     * @internal param $id
     * @return mixed
     */
    public function getAllCartes($campagne_id, $withUnpublished=false){
        //Fetch Carte information
        $sql = "
            SELECT id, campagne_id, name, description, image, published
            FROM carte
            WHERE campagne_id=? ".($withUnpublished?'':' AND published=1')."
        ";
        $result = $this->db->executeQuery($sql, array($campagne_id));

        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Sauvegarde la carte
     * @param $data
     * @throws \Exception
     */
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
        $this->_mustBeMj($data['campagne_id']);

        //Bind the values
        $usedFields = array();
        $usedValues = array();
        foreach($data as $key=>$value){
            if(in_array($key, $fields)){
                $usedValues[$key] = is_array($value) ? json_encode($value):$value;
                $usedFields[] = $key;
            }
        }

        //Then, we save the data, INSERT or UPDATE is automatic
        $sql = "
            INSERT INTO carte
            (".implode(',', $usedFields).")
            VALUES
            (".implode(',', array_map(function($v){return ":$v";}, $usedFields)).")
            ON DUPLICATE KEY UPDATE
            ".implode(',', array_map(function($v){return "$v=:$v";}, $usedFields))."
        ";

        //Prepare SQL
        $stmt = $this->db->prepare($sql);

        //Run it!
        $stmt->execute($usedValues);

        if(isset($data['image'])){
            //If image was passed, we must generate the thumbnail
            $id = isset($data['id']) ? intval($data['id']):$this->db->lastInsertId();
            $this->thumbnailService->generateThumbnail('carte', $id, $data['image']);
        }
    }

    /**
     * Delete a carte
     * @param $id
     */
    public function deleteCarte($id){
        //Fetch carte
        $carte = $this->getCarte($id);
        //Check authorizations
        $this->_mustBeMj($carte['campagne_id']);
        //Delete carte
        $sql = "DELETE FROM carte WHERE id=?";
        $this->db->executeQuery($sql, array($id));
    }
}