<?php
  namespace jdRoll\service\forum;

/**
 * Manage post content
 *
 * @package postService
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */

class PostContentService {

  private $session;
  private $persoService;

  /**
	Constants
	*/

  // rendering of PNJ tag
  const TAG_PNJ = "<a href=\"javascript:void()\" onClick=\"persoModalService.openPerso(%d,%d)\">%s</a>";

  // rendering of CARTE tag
  const TAG_CARTE = "<a href=\"app/%d/#/carte/%d\">%s</a>";

  // rendering of POPUP tag
  const TAG_POPUP = "<a href=\"#!\" rel=\"popover\" data-title=\"%s\" data-content=\"%s\" data-placement=\"bottom\" data-trigger=\"hover\">%s</a>";

  // rendering of PRV tag header
  const TAG_PRV_HEADER = "<b><p size=\"small\">Visible par : MJ, %s</p></b>";

  // Denied access message for PRV tag
  const TAG_PRV_ACCESS_DENIED = "<br>Une partie de ce message est en privée et ne vous est pas accessible.<br>";

  //rendering of PRV tag
  const TAG_PRV_ZONE = "<div style=\"background-color: #EBEADD; background-color: rgba(230, 230, 230, 0.4); padding:15px \">%s</div>";

  // HIDE tag default title
  const TAG_HIDE_EMPY_TITLE = "Informations masquées";

  // rendering of HIDE tag
  const TAG_HIDE = "<div><a href=\"javascript:void()\" onclick=\"if (this.parentNode.getElementsByTagName('div')[0].style.display != '') { this.parentNode.getElementsByTagName('div')[0].style.display = ''; } else { this.parentNode.getElementsByTagName('div')[0].style.display = 'none'; }\"><u>%s</u></a><div style=\"display:none\">%s</div></div>";


  public function __construct($persoService,$session) {
    $this->session = $session;
    $this->persoService = $persoService;
  }

  public function transformAllTag($postContent, $persoName, $is_mj,$campagne_id)
  {
    $perso = $this->persoService->getPersonnage(false, $campagne_id, $this->session->get('user')['id']);
    if ($perso == null) {
      $perso = [];
    }
    $postContent = $this->_transformPrivateZoneForMessage($postContent, $persoName, $this->session->get('user')['login'], $perso, $is_mj);
    $postContent = $this->_replace_hide($postContent);
    $postContent = $this->_transformPopupZone($postContent);
    $postContent = $this->_transformPNTag($postContent,$campagne_id);
    $postContent = $this->_transformCarteTag($postContent,$campagne_id);
    return $postContent;
  }

  private function _transformPNTag($postContent,$campagne_id)
  {

    return preg_replace_callback('#\[pnj=(.*)\](.*)\[/pnj\]#isU',
                                                   function ($matches) use($campagne_id) {

                                                     $perso_id = $this->persoService->getPNJInCampagneByName($campagne_id,html_entity_decode($matches[1]));
                                                     if($perso_id == 0)
                                                       return $matches[2];
                                                     else
                                                       return sprintf(self::TAG_PNJ,$campagne_id,$perso_id,$matches[2]);

                                                   },
                                                   $postContent
                                                  );
  }

  private function _transformCarteTag($postContent,$campagne_id)
  {

    return preg_replace_callback('#\[carte=(.*)\](.*)\[/carte\]#isU',
                                                   function ($matches) use($campagne_id) {
                                                     return sprintf(self::TAG_CARTE,$campagne_id,$matches[1],$matches[2]);
                                                   },
                                                   $postContent
                                                  );
  }


  private function _transformPopupZone($postContent)
  {
    return preg_replace_callback('#\[popup=(.*),(.*)\](.*)\[/popup\]#isU',
                                                   function ($matches) {
                                                     return sprintf(self::TAG_POPUP,$matches[1],$matches[3],$matches[2]);
                                                   },
                                                   $postContent
                                                  );

  }

  private function _transformPrivateZoneForMessage($postContent, $postPersoName, $login, $persos, $is_mj) {
    $isThereAPrivateForMe = false;
    $postForTest = preg_replace_callback('#\[(private|prv)(?:=(.*,?))?\](.*)\[/\1\]#isU',
                                         function ($matches) use($is_mj,$login,$persos,&$isThereAPrivateForMe,$postContent, $postPersoName){
                                           foreach($persos as $perso) {
                                             if(!isset($perso['name']) || strcasecmp($perso['name'],$postPersoName) == 0)
                                             {
                                               $isThereAPrivateForMe = true;
                                             }
                                             else
                                             {
                                               $users = preg_split("#,#", html_entity_decode($matches[2]));
                                               foreach($users as $user)
                                               {
                                                 if(strcasecmp($login,trim($user)) == 0 || strcasecmp($perso['name'],trim($user)) == 0)
                                                 {
                                                   $isThereAPrivateForMe=true;
                                                   break;
                                                 }
                                               }
                                             }
                                           }
                                           if ($is_mj) {
                                             $isThereAPrivateForMe = true;
                                           }
                                           return '';
                                         },
                                         $postContent
                                        );

    $postTrim = strip_tags($postForTest);
    //Trop gourmand ? A optimiser ?
    $postTrim = strtr($postTrim, array_flip(get_html_translation_table(HTML_ENTITIES)));
    $postTrim = trim($postTrim,"\t\n\r\0\x0B\xC2\xA0\xE2\x80\x89\xE2\x80\x83\xE2\x80\x82");

    $postSize = strlen($postTrim);

    $postContent = preg_replace_callback('#\[(private|prv)(?:=(.*,?))?\](.*)\[/\1\]#isU',
                                                  function ($matches) use ($is_mj,$login,$persos,$postContent,$postPersoName,$postSize,$isThereAPrivateForMe){

                                                    $txt = sprintf(self::TAG_PRV_HEADER,html_entity_decode($matches[2]));
                                                    $ret = '';
                                                    foreach($persos as $perso) {
                                                      if (!isset($perso['name']) || strcasecmp($perso['name'], $postPersoName) == 0) {
                                                        $ret = $this->_getPrivateZone($txt . $matches[3]);
                                                      } else {
                                                        $users = preg_split("#,#", html_entity_decode($matches[2]));
                                                        foreach ($users as $user) {
                                                          if (strcasecmp($login, trim($user)) == 0 || strcasecmp($perso['name'], trim($user)) == 0) {
                                                            $ret = $this->_getPrivateZone($txt . $matches[3]);
                                                            break;
                                                          }
                                                        }
                                                      }
                                                    }
                                                    if ($is_mj) {
                                                      $ret = $this->_getPrivateZone($txt . $matches[3]);
                                                    }

                                                    if(!$isThereAPrivateForMe)
                                                    {
                                                      if($postSize == 0)
                                                        $ret = $this->_getPrivateZone(self::TAG_PRV_ACCESS_DENIED);
                                                    }

                                                    return $ret;
                                                  },
                                                  $postContent
                                                 );
    return $postContent;
  }


  private function _getPrivateZone($txt) {
    return sprintf(self::TAG_PRV_ZONE,$txt);
  }


  private function _replace_hide($postContent)
  {
    return preg_replace_callback('#\[hide(?:=(.*?))?\]((?:(?>[^\[]*)|(?R)|\[)*)\[/hide\]#is',
                                                   function ($matches) {

                                                     $txt = '';
                                                     $m = '';
                                                     if($matches[1] != '')
                                                       $txt = $matches[1];
                                                     else
                                                       $txt = self::TAG_HIDE_EMPY_TITLE;

                                                     if(strpos($matches[2],"[/hide]"))
                                                       $m = $this->_replace_hide($matches[2]);
                                                     else
                                                       $m = $matches[2];

                                                     return sprintf(self::TAG_HIDE,$txt,$m);

                                                   },
                                                   $postContent
                                                  );

  }
}

?>
