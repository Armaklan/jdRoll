<?php
namespace jdRoll\service;

/**
 * Manage character sheet
 *
 * @package sheetService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


class SheetService {

    private $db;
    private $session;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addNewTemplate($request) {
	
		
        $sql = "INSERT INTO SHEET_FIELD_TEMPLATE
				(campagne_id,name,type,font_color)
				VALUES
				(:campagneId, :templateName,:templateType,:templateFontColor)";

        $stmt = $this->db->prepare($sql);
		$stmt->bindValue("campagneId", $request->get('campagneId'));
        $stmt->bindValue("templateName", $request->get('templateName'));
        $stmt->bindValue("templateType", $request->get('templateType'));
		$stmt->bindValue("templateFontColor", $request->get('templateFontColor'));
        $stmt->execute();
    }
	
	
	public function getTemplatesByCampagneId($id) {
		$sql = "SELECT * FROM SHEET_FIELD_TEMPLATE WHERE campagne_id = ?";
		$templates = $this->db->fetchAssoc($sql, array($id));
		return $templates;
	}
	

}

?>
