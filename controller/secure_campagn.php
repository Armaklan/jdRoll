<?php

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	/*
	    Controller de campagne (sécurisé)
	*/
	$securedCampagneController = $app['controllers_factory'];
	$securedCampagneController->before($mustBeLogged);

	$securedCampagneController->get('/new', function() use($app) {
	    $campagne = $app['campagneService']->getBlankCampagne();
	    return $app->render('campagne_form.html.twig', ['campagne' => $campagne, 'error' => ""]);
	})->bind("campagne_new");

	$securedCampagneController->get('/{id}/edit', function($id) use($app) {
	    $campagne = $app['campagneService']->getCampagne($id);
	    return $app->render('campagne_form.html.twig', ['campagne' => $campagne, 'error' => ""]);
	})->bind("campagne_edit");

	$securedCampagneController->get('/join/{id}', function($id) use($app) {
		try {
		    $campagne = $app['campagneService']->addJoueur($id, $app['session']->get('user')['id']);
		    return $app->redirect($app->path('campagne_my_list'));
		} catch (Exception $e) {
			$campagnes = $app['campagneService']->getOpenCampagne();
    		return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => $e->getMessage()]);
		}
	})->bind("campagne_join");

	$securedCampagneController->get('/quit/{id}', function($id) use($app) {
		try {
		    $campagne = $app['campagneService']->removeJoueur($id, $app['session']->get('user')['id']);
		    return $app->redirect($app->path('campagne_my_list'));
		} catch (Exception $e) {
			$campagnes = $app['campagneService']->getOpenCampagne();
    		return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => $e->getMessage()]);
		}
	})->bind("campagne_quit");
	
	$securedCampagneController->get('/ban/{id}/{user_id}', function($id, $user_id) use($app) {
		$campagne = $app['campagneService']->removeJoueur($id, $user_id);
		return $app->redirect($app->path('campagne', array('id' => $id )));
	})->bind("campagne_ban");

	$securedCampagneController->get('/my_list', function() use($app) {
	    $campagnes = $app['campagneService']->getMyCampagnes();
	    $campagnesPj = $app['campagneService']->getMyPjCampagnes();
	    $campagnesMjArchive = $app['campagneService']->getMyMjArchiveCampagnes();
	    $campagnesPjArchive = $app['campagneService']->getMyPjArchiveCampagnes();
	    return $app->render('campagne_my_list.html.twig', ['campagnes' => $campagnes, 'campagnes_pj' => $campagnesPj, 
	    		'campagnes_mj_archive' => $campagnesMjArchive, 'campagnes_pj_archive' => $campagnesPjArchive, 'error' => ""]);
	})->bind("campagne_my_list");

	$securedCampagneController->post('/save', function(Request $request) use($app) {
	    try {
	        if ($request->get('id') == '') {
	            $app['campagneService']->createCampagne($request);
	            return $app->redirect($app->path('campagne_my_list'));
	        } else {
	            $app['campagneService']->updateCampagne($request);
	            return $app->redirect($app->path('campagne_my_list'));
	        }
	    } catch (Exception $e) {
	        $campagne = $app['campagneService']->getFormCampagne($request);
	        return $app->render('campagne_form.html.twig', ['campagne' => $campagne, 'error' => $e->getMessage()]);    
	    }
	})->bind("campagne_save");
	
	$securedCampagneController->get('/sidebar/{campagne_id}', function(Request $request, $campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$perso = $app['persoService']->getPersonnage(true, $campagne_id, $player_id);
		$allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id);
		$campagne = $app['campagneService']->getCampagne($campagne_id);
		return $app->render('sidebar_campagne.html.twig', ['campagne_id' => $campagne_id, 'perso' => $perso, 'allPerso' => $allPerso, 'campagne' => $campagne]);
	})->bind("sidebar_campagne");
	
	$securedCampagneController->get('/sidebarmj/{campagne_id}', function(Request $request, $campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id);
		$campagne = $app['campagneService']->getCampagne($campagne_id);
		return $app->render('sidebar_mj_campagne.html.twig', ['campagne_id' => $campagne_id, 'allPerso' => $allPerso, 'campagne' => $campagne]);
	})->bind("sidebar_campagne_mj");
	
	$securedCampagneController->get('/sidebar_large/{campagne_id}', function(Request $request, $campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$perso = $app['persoService']->getPersonnage(true, $campagne_id, $player_id);
		$allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id);
		$campagne = $app['campagneService']->getCampagne($campagne_id);
		return $app->render('sidebar_campagne_large.html.twig', ['campagne_id' => $campagne_id, 'perso' => $perso, 'allPerso' => $allPerso, 'campagne' => $campagne]);
	})->bind("sidebar_campagne_large");
	
	$securedCampagneController->get('/sidebarmj_large/{campagne_id}', function(Request $request, $campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id);
		$campagne = $app['campagneService']->getCampagne($campagne_id);
		return $app->render('sidebar_mj_campagne_large.html.twig', ['campagne_id' => $campagne_id, 'allPerso' => $allPerso, 'campagne' => $campagne]);
	})->bind("sidebar_campagne_mj_large");
	
	$securedCampagneController->get('/dicer/view/{campagne_id}', function($campagne_id) use($app) {
		$jets = $app['dicerService']->getDice($campagne_id);
		return $app->render('dicer.html.twig', ['campagne_id' => $campagne_id, 'jets' => $jets]);
	})->bind("print_dicer");
	
	$securedCampagneController->post('/dicer/{campagne_id}/{topic_id}', function(Request $request, $campagne_id, $topic_id) use($app) {
		try {
			$player_id = $app['session']->get('user')['id'];
			$param = $request->get('param');
			$description = $request->get('description');
			$result = $app['dicerService']->launchDice($campagne_id,$param, $description);
			if($topic_id != 0) {
				$player = $app['userService']->getCurrentUser();
				$name = $player['username'];
				$post_id = $app['postService']->createDicerPost($topic_id, " $name a lancé $param et a obtenu : $result . <br> Description : $description ");
			} 
			return $result;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	})->bind("dicer");
	
	$app->mount('/campagne', $securedCampagneController);

?>