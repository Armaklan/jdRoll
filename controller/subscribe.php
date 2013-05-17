<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
    Controller d'inscription
*/
$subscribeController = $app['controllers_factory'];
$subscribeController->get('/', function() use($app) {
    $user = array('username' => '', 'mail' => '');
    return $app->render('subscribe.html.twig', ['user' => $user, 'error' => ""]);
})->bind("subscribe");

$subscribeController->post('/save', function(Request $request) use($app) {
    try {
        $user = $app["userService"]->subscribeUser($request);
        return $app->render('login.html.twig', ['error' => "Création de l'utilisateur réussit."]);
    } catch (Exception $e) {
        $user = array("username" => $request->get('username'), "mail" => $request->get('mail'));
        return $app->render('subscribe.html.twig', ['user' => $user, 'error' => $e->getMessage()]);
    }
})->bind("subscribe_save");

$app->mount('/subscribe', $subscribeController);


?>