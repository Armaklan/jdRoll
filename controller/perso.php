<?php

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	/*
	    Controller de campagne (sécurisé)
	*/
	$persoController = $app['controllers_factory'];
	$persoController->before($mustBeLogged);

	$persoController->get('/edit/{campagne_id}', function($campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
	    $perso = $app['persoService']->getPersonnage($campagne_id, $player_id);
	    return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => ""]);
	})->bind("perso_edit");

	$persoController->post('/save/{campagne_id}', function($campagne_id, Request $request) use($app) {
		$player_id = $app['session']->get('user')['id'];
		try {
	    	$app['persoService']->updatePersonnage($campagne_id, $player_id, $request);
		} catch(Exception $e) {
			$perso = $app['persoService']->getPersonnage($campagne_id, $player_id);
			return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => $e->getMessage()]);
		}
	    $perso = $app['persoService']->getPersonnage($campagne_id, $player_id);
	    return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => ""]);
	})->bind("perso_save");


	$app->mount('/perso', $persoController);

?>