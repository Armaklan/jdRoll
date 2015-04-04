<?php
/**
 * Campagne controller for public operation
 *
 * @package public_campagn
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;

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
    $stats = $app["postService"]->getStatForOnGame($id);
    return $app->render('game/presentation.html.twig', [
        'campagne' => $campagne,
        'participants' => $participants,
    	'is_mj' => $is_mj,
        'is_admin' => $is_admin,
        'is_participant' => $is_participant,
        'stat_post' => $stats,
        'error' => ""]);
})->bind("campagne");

$publicCampagneController->get('/config/{id}', function($id) use($app) {
	$campagne = $app['campagneService']->getCampagneConfig($id);
	return $app->render('game/config/css.html.twig', ['campagne_config' => $campagne, 'error' => ""]);
})->bind("campagne_config");


$publicCampagneController->post('/favoris', function(Request $request) use($app) {
            $campagne = $request->get("campagne");
		    $joueur = $app['session']->get('user')['id'];
            $statut = $request->get("statut");

            if($statut == 0) {
                $app["favorisService"]->removeFavoris($campagne, $joueur);
            } else {
                $app["favorisService"]->addFavoris($campagne, $joueur);
            }
            return "";
        })->bind("favoris");


$app->mount('/campagne', $publicCampagneController);

?>
