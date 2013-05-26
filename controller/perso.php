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
		$perso = $app['persoService']->getPersonnageById($perso_id);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id, 'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_edit_mj");
	
	$persoController->get('/view/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$perso = $app['persoService']->getPersonnageById($perso_id);
		if($perso["user_id"] != $app["session"]->get("user")["id"]) {
			$is_mj = $app["campagneService"]->isMj($campagne_id);
			return $app->render('perso_public.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
		} else {
			return $app->redirect($app->path('perso_view_all', ['campagne_id' => $campagne_id]));
		}
	})->bind("perso_view");
	
	$persoController->get('/view_all_mj/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$perso = $app['persoService']->getPersonnageById($perso_id);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		return $app->render('perso_view_all.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_view_all_mj");
	
	$persoController->get('/view_all/{campagne_id}', function($campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$perso = $app['persoService']->getPersonnage(true,$campagne_id, $player_id);
		return $app->render('perso_view_all.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_view_all");

	$persoController->post('/save/{campagne_id}', function($campagne_id, Request $request) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$perso_id = $request->get('perso_id');
		try {
			if ($perso_id != "") {
	    		$app['persoService']->updatePersonnage($campagne_id, $perso_id, $request);
	    		$perso = $app['persoService']->getPersonnageById($perso_id);
	    		return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
			} else {
				$app['persoService']->insertPNJ($campagne_id, $request);
				return $app->redirect($app->path('perso_pnj', ['campagne_id' => $campagne_id]));
			}
		} catch(Exception $e) {
			$perso = $app['persoService']->getPersonnageById($perso_id);
			return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => $e->getMessage(), 'is_mj' => $is_mj]);
		}
	})->bind("perso_save");

	$persoController->get('/pnj/{campagne_id}', function($campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$persos = $app['persoService']->getPNJInCampagne($campagne_id);
		return $app->render('perso_list.html.twig', ['campagne_id' => $campagne_id,'persos' => $persos, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_pnj");
	
	$persoController->get('/pnj/{campagne_id}/remove', function($campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$perso = $app['persoService']->getBlankPnj($campagne_id);
		return $app->render('perso_form.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_pnj_add");
	
	$persoController->get('/remove/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$error = "";
		if ($is_mj) {
			try {
				$app["persoService"]->deletePersonnage($perso_id);
			} catch (Exception $e) {
				$error = $e->getMessage();
			}		
		} else {
			$error = "Vous n'êtes pas le MJ de cette partie";
		}
		$persos = $app['persoService']->getPNJInCampagne($campagne_id);
		return $app->render('perso_list.html.twig', ['campagne_id' => $campagne_id,'persos' => $persos, 'error' => $error, 'is_mj' => $is_mj]);
	})->bind("perso_pnj_del");
	
	$persoController->get('/attach/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$perso = $app['persoService']->getPersonnageById($perso_id);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$users = $app["campagneService"]->getParticipant($campagne_id);
		return $app->render('perso_attach_form.html.twig', ['campagne_id' => $campagne_id, 'perso' => $perso, 'error' => "", 'is_mj' => $is_mj, 'users' => $users]);
	})->bind("attach");
	
	$persoController->post('/save_attach/{campagne_id}', function($campagne_id, Request $request) use($app) {
		$perso = $app['persoService']->attachPersonnage($campagne_id, $request->get('user_id'), $request->get('perso_id'));
		return $app->redirect($app->path('perso_pnj', ['campagne_id' => $campagne_id]));
	})->bind("save_attach");

	$app->mount('/perso', $persoController);

?>