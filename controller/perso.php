<?php

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	/*
	    Controller de campagne (sécurisé)
	*/
	$persoController = $app['controllers_factory'];
	$persoController->before($mustBeLogged);

	$persoController->get('/edit/{campagne_id}', function($id) use($app) {
		$perso_id = $['app']->get('user')['id'];
	    $perso = $app['persoService']->getPersonnage($campagne_id, $perso_id);
	    return $app->render('perso_form.html.twig', ['perso' => $perso, 'error' => ""]);
	})->bind("perso_edit");

	$app->mount('/perso', $persoController);

?>