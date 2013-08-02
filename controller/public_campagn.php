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

$publicCampagneController->get('/list/archive', function() use($app) {
	$campagnes = $app['campagneService']->getArchiveCampagne();
	return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => ""]);
})->bind("campagne_list_archive");

$publicCampagneController->get('/list/prepa', function() use($app) {
	$campagnes = $app['campagneService']->getPrepaCampagne();
	return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => ""]);
})->bind("campagne_list_prepa");

$publicCampagneController->get('/{id}', function($id) use($app) {
    $campagne = $app['campagneService']->getCampagne($id);
    $is_mj = $app["campagneService"]->isMj($id);
    $is_admin = $app["campagneService"]->isAdmin();
    $is_participant = $app["campagneService"]->isParticipant($id);
    $participants = $app["campagneService"]->getParticipant($id);
    return $app->render('campagne.html.twig', ['campagne' => $campagne, 'participants' => $participants,
    		'is_mj' => $is_mj, 'is_admin' => $is_admin, 'is_participant' => $is_participant, 'error' => ""]);
})->bind("campagne");

$publicCampagneController->get('/config/{id}', function($id) use($app) {
	$campagne = $app['campagneService']->getCampagneConfig($id);
	return $app->render('campagne_config.html.twig', ['campagne_config' => $campagne, 'error' => ""]);
})->bind("campagne_config");

$app->mount('/campagne', $publicCampagneController);

?>
