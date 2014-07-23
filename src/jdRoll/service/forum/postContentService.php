<?php
namespace jdRoll\service;

/**
 * Manage post content
 *
 * @package postService
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */

class PostContentService {

    private $session;
	private $db;

    public function __construct($db,$session) {
        $this->session = $session;
		$this->db = $db;
    }
	
	public function transformAllTag(&$post,$perso, $is_mj,$campagne_id)
	{
		 $this->_transformPrivateZoneForMessage($post, $this->session->get('user')['login'], $perso, $is_mj);
         $this->_replace_hide($post);
		 $this->_transformPopupZone($post);
		 $this->_transformPNTag($post,$campagne_id);
	}

	private function _transformPNTag(&$post,$campagne_id)
	{
	
		$post['post_content']  = preg_replace_callback('#\[pnj=(.*)\](.*)\[/pnj\]#isU',
				function ($matches) use($campagne_id) {

					$sql = "SELECT id from personnages where personnages.name = :name and personnages.campagne_id = :campagne";

					$perso_id = $this->db->fetchColumn($sql, array("name" => $matches[1], "campagne" => $campagne_id));
					return '<a href="javascript:void()" onClick="persoModalService.openPerso(' . $campagne_id . ',' . $perso_id . ')">' . $matches[2] . '</a>';

				},
				$post['post_content']
			);

		return $post;
	}                                                                                       
	

	private function _transformPopupZone(&$post)
	{
		$post['post_content']  = preg_replace_callback('#\[popup=(.*),(.*)\](.*)\[/popup\]#isU',
				function ($matches) {

					return '<a href="#!" rel="popover" data-title="' . $matches[1] . '" data-content="' . $matches[3] . '" data-placement="bottom" data-trigger="hover">' . $matches[2] . '</a>';

				},
				$post['post_content']
			);

		return $post;
	}
	
	private function _transformPrivateZoneForMessage(&$post, $login, $perso, $is_mj) {
        $isThereAPrivateForMe = false;
        $postForTest = preg_replace_callback('#\[(private|prv)(?:=(.*,?))?\](.*)\[/\1\]#isU',
        function ($matches) use($is_mj,$login,$perso,&$isThereAPrivateForMe,$post){

                if($is_mj || !isset($perso['name']) || strcasecmp($perso['name'],$post['perso_name']) == 0)
                {
                        $isThereAPrivateForMe = true;
                }
                else
                {
                        $users = preg_split("#,#", $matches[2]);
                        foreach($users as $user)
                        {
                                if(strcasecmp($login,trim($user)) == 0 || strcasecmp($perso['name'],trim($user)) == 0)
                                {
                                        $isThereAPrivateForMe=true;
                                        break;
                                }
                        }
                }
                return '';
                },
                $post['post_content']
        );

        $postTrim = strip_tags($postForTest);
        //Trop gourmand ? A optimiser ?
        $postTrim = strtr($postTrim, array_flip(get_html_translation_table(HTML_ENTITIES)));
        $postTrim = trim($postTrim,"\t\n\r\0\x0B\xC2\xA0\xE2\x80\x89\xE2\x80\x83\xE2\x80\x82");

        $postSize = strlen($postTrim);

        $post['post_content'] = preg_replace_callback('#\[(private|prv)(?:=(.*,?))?\](.*)\[/\1\]#isU',
        function ($matches) use ($is_mj,$login,$perso,$post,$postSize,$isThereAPrivateForMe){

                $txt = '<b><p size="small">Visible par : MJ, ' . $matches[2] . '</p></b>';
                $ret = '';
                if($is_mj || !isset($perso['name']) || strcasecmp($perso['name'],$post['perso_name']) == 0)
                {
                    $ret = $this->_getPrivateZone($txt . $matches[3]);
                }
                else
                {
                        $users = preg_split("#,#", $matches[2]);
                        foreach($users as $user)
                        {
                                if(strcasecmp($login,trim($user)) == 0 || strcasecmp($perso['name'],trim($user)) == 0)
                                {
                                        $ret = $this->_getPrivateZone($txt . $matches[3]);
                                        break;
                                }
                        }
                }

                if(!$isThereAPrivateForMe)
                {
                        if($postSize == 0)
                                $ret = $this->_getPrivateZone('<br>Une partie de ce message est en privée et ne vous est pas accessible.<br>');
                }

                return $ret;
                },
                $post['post_content']
        );
        return $post;
}


	private function _getPrivateZone($txt) {
    return '<div style="background-color: #EBEADD; background-color: rgba(230, 230, 230, 0.4); padding:15px ">'. $txt . '</div>';
	}


	private function _replace_hide(&$post)
	{
		$post['post_content']  = preg_replace_callback('#\[hide(?:=(.*?))?\]((?:(?>[^\[]*)|(?R)|\[)*)\[/hide\]#is',
					function ($matches) {

						$txt = '';
						$m = '';
						if($matches[1] != '')
							$txt = $matches[1];
						else
							$txt = 'Informations masquées';

						if(strpos($matches[2],"[/hide]"))
							$m = replace_hide($matches[2]);
						else
							$m = $matches[2];

						return '<div><a href="javascript:void()" onclick="if (this.parentNode.getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'\'; } else { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'none\'; }"><u>' . $txt . '</u></a><div style="display:none">' . $m . '</div></div>';;

					},
					$post['post_content']
				);

		return $post;
	}
}

?>
