<?php
/**
 * Control subscribe of new user
 *
 * @package subscribe
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;

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
        $app["userService"]->subscribeUser($request);
        $listUser[0] = $request->get('username');
        $content = "
            <p>Bonjour et bienvenue sur la plateforme jdRoll !</p>
            <br>
            <p>Dans un premier temps, je vous proposer d'aller vous présenter sur le Forum dans le fils 'Présentation'. Vous pourrez ensuite copier-coller cette présentation dans votre profil (histoire que tout le monde puisse la retrouver facilement).</p>
            <p>N'hésitez pas à faire un petit coucou sur le tchat (en bas de la page d'accueil ou en bas du Forum).</p>
            <br>
            <p>Nous espérons que vous trouverez de quoi vous amuser sur ce forum.</p>
            <br>
            <p>Ludiquement</p>
        ";
        $app['messagerieService']->sendMessageWith(0, "Système", "Bienvenu sur jdRoll ! ", $content, $listUser);
        return $app->render('login.html.twig', ['error' => "Création de l'utilisateur réussit.", 'url' => '!']);
    } catch (Exception $e) {
        $user = array("username" => $request->get('username'), "mail" => $request->get('mail'));
        return $app->render('subscribe.html.twig', ['user' => $user, 'error' => $e->getMessage()]);
    }
})->bind("subscribe_save");

$app->mount('/subscribe', $subscribeController);


?>
