<?php
	/*
	    Controller de campagne (sécurisé)
	*/
	$securedCampagneController = $app['controllers_factory'];
	$securedCampagneController->before($mustBeLogged);
	$securedCampagneController->get('/new', function() use($app) {
	    $campagne = $app['campagneService']->getBlankCampagne();
	    return $app->render('campagne_form.html.twig', ['campagne' => $campagne, 'error' => ""]);
	})->bind("campagne_new");

	$securedCampagneController->post('/save', function(Request $request) use($app) {
	    try {
	        if ($request->get('id') == '') {
	            $app['campagneService']->createCampagne($request);
	            return $app->redirect($app->path('homepage'));
	        } else {
	            $app['campagneService']->updateCampagne($request);
	            return $app->redirect($app->path('homepage'));
	        }
	    } catch (Exception $e) {
	        $campagne = $app['campagneService']->getFormCampagne($request);
	        return $app->render('campagne_form.html.twig', ['campagne' => $campagne, 'error' => $e->getMessage()]);    
	    }
	})->bind("campagne_save");
	$app->mount('/campagne', $securedCampagneController);

?>