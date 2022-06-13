<?php
  namespace jdRoll\service\campagne;

/**
 * Manage Campagne Information
 *
 * @package campagneService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


class FavorisService {

  private $db;

  public function __construct($db)  {
    $this->db = $db;
  }

  public function addFavoris($campagne, $joueur) {
    $sql = "INSERT INTO campagne_favoris
                (campagne_id, user_id)
                VALUES
                (:campagne, :joueur)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue("campagne", $campagne);
    $stmt->bindValue("joueur", $joueur);
    $stmt->execute();
  }

  public function removeFavoris($campagne, $joueur) {
    $sql = "DELETE FROM campagne_favoris
                WHERE
                campagne_id = :campagne
                AND user_id = :joueur ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue("campagne", $campagne);
    $stmt->bindValue("joueur", $joueur);
    $stmt->execute();
  }

  public function isFavoris($campagne, $joueur) {
    $sql = "SELECT user_id
                FROM campagne_favoris
                WHERE
                campagne_id = :campagne
                AND user_id = :joueur";
    $result = $this->db->fetchColumn($sql, array('joueur' => $joueur, 'campagne' => $campagne ), 0);
    return ($result != null);
  }

  public function getFavorisInCampagne($campagne) {
    $sql = "SELECT user_id
                FROM campagne_favoris
                WHERE
                campagne_id = :campagne";
    return $this->db->fetchAll($sql, array('campagne' => $campagne ));
  }

}

