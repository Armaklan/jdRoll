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
		$is_mj = $app["campagneService"]->isMj($campagne_id);
	    $perso = $app['persoService']->getPersonnage(true,$campagne_id, $player_id);
	    return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_edit");
	
	$persoController->get('/edit/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$perso = $app['persoService']->getPersonnage(false,$campagne_id, $player_id);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_edit_mj");
	
	$persoController->get('/view/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$player_id = $perso_id;
		$perso = $app['persoService']->getPersonnage(false,$campagne_id, $player_id);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		return $app->render('perso_public.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_view");

	$persoController->post('/save/{campagne_id}', function($campagne_id, Request $request) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		try {
	    	$app['persoService']->updatePersonnage($campagne_id, $player_id, $request);
		} catch(Exception $e) {
			$perso = $app['persoService']->getPersonnage(false,$campagne_id, $player_id);
			return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => $e->getMessage(), 'is_mj' => $is_mj]);
		}
	    $perso = $app['persoService']->getPersonnage(false,$campagne_id, $player_id);
	    return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_save");


	$app->mount('/perso', $persoController);

?>