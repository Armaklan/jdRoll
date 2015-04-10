<?php
  namespace jdRoll\service\campagne;

/**
 * Manage Campagne Information
 *
 * @package campagneService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


class CampagneConfigService {

  private $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getFormCampagneConfig($request) {
    $campagne = array();
    $campagne['campagne_id'] = $request->get('campagne_id');
    $campagne['banniere'] = $request->get('banniere');
    $campagne['hr'] = $request->get('hr');
    $campagne['odd_line_color'] = $request->get('odd_line_color');
    $campagne['even_line_color'] = $request->get('even_line_color');
    $campagne['sidebar_color'] = $request->get('sidebar_color');
    $campagne['link_color'] = $request->get('link_color');
    $campagne['text_color'] = $request->get('text_color');
    $campagne['default_perso_id'] = $request->get('default_perso_id');
    $campagne['link_sidebar_color'] = $request->get('link_sidebar_color');
    $campagne['template'] = $request->get('template');
    $campagne['template_html'] = $request->get('hiddenInput');
    $campagne['template_fields'] = $request->get('hiddenInputFields');
    $campagne['template_img'] = $request->get('imgBG');
    $campagne['sidebar_text'] = $request->get('sidebar_text');
    return $campagne;
  }

  public function createCampagneConfig($campagne) {
    $sql = "INSERT INTO campagne_config
				(campagne_id, banniere, hr, odd_line_color, even_line_color, sidebar_color, link_sidebar_color, link_color, text_color, default_perso_id, template, sidebar_text, widgets)
				VALUES
				(:campagne, '', '', '', '', '', '', '', '', '', '', '', '[]')";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue("campagne", $campagne);
    $stmt->execute();
  }

  public function updateCampagneConfigSheet($request) {
    $sql = "UPDATE campagne_config
    			SET
    			template = :template,
				template_html = :template_html,
				template_fields = :template_fields,
				template_img = :template_img
    			WHERE
    			campagne_id = :campagne";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue("campagne", $request->get('id'));
    $stmt->bindValue("template", $request->get('template'));
    $stmt->bindValue("template_html",$request->get('hiddenInput'));
    if($request->get('typeFiche') == 0)
      $stmt->bindValue("template_img",NULL);
    else
      $stmt->bindValue("template_img",$request->get('imgBG'));
    $stmt->bindValue("template_fields",$request->get('hiddenInputFields'));
    $stmt->execute();
  }

  public function updateWidgetsConfig($campagne, $widgets) {
    $sql = "UPDATE campagne_config
    			SET
    			widgets = :widgets
    			WHERE
    			campagne_id = :campagne";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue("campagne", $campagne);
    $stmt->bindValue("widgets", $widgets);
    $stmt->execute();
  }



  public function updateCampagneConfigTheme($request) {
    $sql = "UPDATE campagne_config
    			SET
    			banniere = :banniere,
    			hr = :hr,
    			odd_line_color = :odd_line_color,
    			even_line_color = :even_line_color,
    			sidebar_color = :sidebar_color,
    			link_color = :link_color,
    			text_color = :text_color,
    			link_sidebar_color = :link_sidebar_color,
                dialogue_color = :dialogue_color,
                pensee_color = :pensee_color,
                rp1_color = :rp1_color,
                rp2_color = :rp2_color,
                quote_color = :quote_color
    			WHERE
    			campagne_id = :campagne";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue("campagne", $request->get('campagne_id'));
    $stmt->bindValue("banniere", $request->get('banniere'));
    $stmt->bindValue("hr", $request->get('hr'));
    $stmt->bindValue("odd_line_color", $request->get('odd_line_color'));
    $stmt->bindValue("even_line_color", $request->get('even_line_color'));
    $stmt->bindValue("sidebar_color", $request->get('sidebar_color'));
    $stmt->bindValue("link_color", $request->get('link_color'));
    $stmt->bindValue("text_color", $request->get('text_color'));
    $stmt->bindValue("dialogue_color", $request->get('dialogue_color'));
    $stmt->bindValue("pensee_color", $request->get('pensee_color'));
    $stmt->bindValue("rp1_color", $request->get('rp1_color'));
    $stmt->bindValue("rp2_color", $request->get('rp2_color'));
    $stmt->bindValue("quote_color", $request->get('quote_color'));
    $stmt->bindValue("link_sidebar_color", $request->get('link_sidebar_color'));
    $stmt->execute();
  }

  public function updateCampagneConfigDivers($request) {
    $sql = "UPDATE campagne_config
    			SET
    			sidebar_text = :sidebar_text,
    			default_perso_id = :default_perso_id,
          default_dice = :default_dice
    			WHERE
    			campagne_id = :campagne";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue("campagne", $request->get('campagne_id'));
    $stmt->bindValue("default_perso_id", $request->get('default_perso_id'));
    $stmt->bindValue("sidebar_text", $request->get('sidebar_text'));
    $stmt->bindValue("default_dice", $request->get('default_dice'));
    $stmt->execute();
  }

  public function getCampagneConfig($id) {
    $sql = "SELECT * FROM campagne_config WHERE campagne_id = ?";
    $campagne = $this->db->fetchAssoc($sql, array($id));
    return $campagne;
  }

}
