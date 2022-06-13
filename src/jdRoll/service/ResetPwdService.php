<?php
namespace jdRoll\service;

/**
 * Manage process of reset password
 *
 * @package resetPwdService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


class ResetPwdService {

	private $db;
	private $session;
	private $messagerieService;

	public function __construct($db, $session, $messagerie)
    {
        $this->db = $db;
        $this->session = $session;
		$this->messagerieService = $messagerie;
    }

	public function checkAleaAndExpiration($alea)
	{
		$sql = "SELECT * from user where reinitAlea = ? and DATE_ADD(reinitDate, INTERVAL 24 HOUR) >= NOW()";
	    $user = $this->db->fetchAssoc($sql, array($alea));

		return $user;

	}

	public function askForReinit($user)
	{
		 // ALTER TABLE  `user` ADD  `reinitDate` DATETIME NULL DEFAULT NULL ,
// ADD  `reinitAlea` VARCHAR( 50 ) NULL DEFAULT NULL ;
		$chars = 'RTa01Fb8IGcdeHf9ghijklmXYKn2-oZJpABq7QrSstLMuEvwx3yU_VW4zCDNOP56';

		for ($i = 0, $alea = ''; $i < 50; $i++) {
			$index = rand(0, strlen($chars) - 1);
			$alea .= substr($chars, $index, 1);
		}


		$sql = "UPDATE user SET reinitDate = NOW(), reinitAlea = :alea WHERE username = :user";


        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("alea", $alea);
		$stmt->bindValue("user", $user);
		$ret = $stmt->execute();
		$count = $stmt->rowCount();
        if($count > 0)
		{

			$listUser[0] = $user;
			$content = "
				<p>Bonjour vous avez demandé la réinitialisatoin de votre mot de passe sur la plateforme JDRoll !</p>
				<br>
				<p>Si vous n'êtes pas l'auteur de cette demande, merci de ne pas tenir compte de ce mail.</p>
				<p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant : http://jdroll.org/resetPwd/". $alea . "</p>
				<p>Ce lien sera valide 24H.
				<br>
			";

			$this->messagerieService->sendMessageToMailBox("[JDRoll] Réinitialisation de mot de passe", $content, $listUser);
			return 0;
	   }
	   else
			return 1;

	}

	public function resetPasswordFromAlea($pwd,$alea)
	{



		$sql = "UPDATE user SET password = md5(:pwd), reinitAlea = NULL WHERE reinitAlea = :alea";


        $stmt = $this->db->prepare($sql);
        $stmt->bindValue("alea", $alea);
		$stmt->bindValue("pwd", $pwd);
		$ret = $stmt->execute();
		$count = $stmt->rowCount();
        if($count > 0)
			return 0;
		else
			return 1;
	}
}
?>
