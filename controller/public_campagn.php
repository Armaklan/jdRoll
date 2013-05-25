<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
    Controller de campagne (public)
*/
$publicCampagneController = $app['controllers_factory'];

$publicCampagneController->get('/list', function() use($app) {
    $campagnes = $app['campagneService']->getOpenCampagne();
    return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => ""]);
})->bind("campagne_list");

$publicCampagneController->get('/list/all', function() use($app) {
	$campagnes = $app['campagneService']->getAllCampagne();
	return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => ""]);
})->bind("campagne_list_all");

$publicCampagneController->get('/{id}', function($id) use($app) {
    $campagne = $app['campagneService']->getCampagne($id);
    return $app->render('campagne.html.twig', ['campagne' => $campagne, 'error' => ""]);
})->bind("campagne");

$publicCampagneController->get('/config/{id}', function($id) use($app) {
	$campagne = $app['campagneService']->getCampagneConfig($id);
	return $app->render('campagne_config.html.twig', ['campagne_config' => $campagne, 'error' => ""]);
})->bind("campagne_config");

$app->mount('/campagne', $publicCampagneController);

?>