<?php
/**
 * Template to control reset password operation
 *
 * @package resetPwd
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;

/*
    Controller d'inscription
*/
$resetPwdController = $app['controllers_factory'];
$resetPwdController->get('/', function() use($app) {

    return $app->render('resetPwd.html.twig', ['warning' => '','error' => ""]);
})->bind("reset");

$resetPwdController->post('/reset', function(Request $request) use($app) {

	$pwd = $request->get('password');
	$alea = $request->get('alea');
	$asked = $app['resetPwdService']->resetPasswordFromAlea($pwd,$alea);
	$error = '';
	$warning = '';
	if($asked == 0)
		$warning = 'Le mot de passe a été changé correctement ! Vous pouvez maintenant vous connecter avec votre nouveau mot de passe';
    else
		$error = "La réinitialisation du mot de passe a échoué";
	return $app->render('resetPwd.html.twig', ['warning' => $warning, 'error' => $error]);
})->bind("reset_pwd");

$resetPwdController->post('/asked', function(Request $request) use($app) {


    $user = $request->get('login');
	$asked = $app['resetPwdService']->askForReinit($user);
	$error = '';
	$warning = '';
	if($asked == 0)
		$warning = 'Un mail contenant les instruction de réinitialisation a été envoyé à l\'adresse configuré dans votre profil.
			Si vous n\'aviez pas configuré votre adresse e-mail ou si vous en avez perdu les accès, vous pouvez toujours plaidez votre cause auprès de nos admins bien aimés : contact@jdroll.org.';
    else
		$error = "Le compte " . $user . " n'existe pas sur la plateforme JDRoll";
	return $app->render('resetPwd.html.twig', ['warning' => $warning, 'error' => $error]);
})->bind("reset_asked");

$resetPwdController->get('/{alea}', function($alea) use($app) {


		$error = '';
		$warning = '';
		$asked = $app['resetPwdService']->checkAleaAndExpiration($alea);
		$changePwd = 0;
		if($asked == null)
		{
			$error = 'Le lien de réinitialisation est expiré ou n\'est pas valide';

		}
		else
		{
			$warning = "Vous pouvez maintenant réinitialiser votre mot de passe.";
			$changePwd = 1;
		}
			return $app->render('resetPwd.html.twig', ['changePwd' => $changePwd,'warning' => $warning, 'error' => $error,'alea' => $alea]);
})->bind("reset_ok");

$app->mount('/resetPwd', $resetPwdController);


?>
