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
        if($request->get('password') != $request->get('password2')) {
            throw new \Exception("Les mots de passes ne correspondent pas");
        }

        $username = $request->get('username');
        $password = $request->get('password');
        $mail = $request->get('mail');

        $app["userService"]->subscribeUser($username, $password, $mail);
        $app["userService"]->login($username, $password);
        return $app->redirect($app->path(markdown, [ 'page' => 'guide']));
    } catch (Exception $e) {
        $user = array("username" => $request->get('username'), "mail" => $request->get('mail'));
        return $app->render('subscribe.html.twig', ['user' => $user, 'error' => $e->getMessage()]);
    }
})->bind("subscribe_save");

$app->mount('/subscribe', $subscribeController);


?>
