<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 Controller de campagne (sécurisé)
*/
$forumController = $app['controllers_factory'];
$forumController->before($mustBeLogged);

$forumController->get('/', function() use($app) {
	return $app->render('blank.html.twig', ['msg' => "Fonctionnalité non implémentée"]);
})->bind("forum");

$forumController->get('/{campagne_id}', function($campagne_id) use($app) {
	$topics = $app["sectionService"]->getAllSectionInCampagne($campagne_id);
	return $app->render('forum_campagne.html.twig', ['campagne_id' => $campagne_id, 'topics' => $topics]);
})->bind("forum_campagne");

$app->mount('/forum', $forumController);

?>