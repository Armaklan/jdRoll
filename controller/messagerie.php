<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 Controller de campagne (sécurisé)
*/
$messagerieController = $app['controllers_factory'];
$messagerieController->before($mustBeLogged);

$messagerieController->get('/', function() use($app) {
	return $app->render('blank.html.twig', ['msg' => "Fonctionnalité non implémentée"]);
})->bind("messagerie");

$app->mount('/messagerie', $messagerieController);

?>