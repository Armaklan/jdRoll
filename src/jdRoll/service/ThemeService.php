<?php
namespace jdRoll\service;

/**
 * Manage pre-defined theme
 *
 * @package themeService
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */


class ThemeService {

	private $db;
	private $logger;

	public function __construct($db, $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

	public function all() {
		$sql = "SELECT *
			FROM theme
			ORDER BY title";
		return $this->db->fetchAll($sql);
	}

	public function byId($id) {
		$sql = "SELECT *
			FROM theme
			WHERE id = ?";
		return $this->db->fetchAssoc($sql, array($id));
	}

}
?>
