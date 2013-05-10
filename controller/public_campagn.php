<?php

/*
    Controller de campagne (public)
*/
$publicCampagneController = $app['controllers_factory'];
$publicCampagneController->get('/list', function() use($app) {
    $campagnes = $app['campagneService']->getAllCampagne();
    return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => ""]);
})->bind("campagne_list");

$publicCampagneController->get('/{id}', function($id) use($app) {
    $campagne = $app['campagneService']->getCampagne($id);
    return $app->render('campagne.html.twig', ['campagne' => $campagne, 'error' => ""]);
})->bind("campagne");
$app->mount('/campagne', $publicCampagneController);

?>