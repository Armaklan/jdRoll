<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
    Controller du profile
*/
$profileController = $app['controllers_factory'];
$profileController->before($mustBeLogged);

$profileController->get('/', function() use($app) {
    $user = $app["userService"]->getCurrentUser();
    return $app->render('my_profile.html.twig', ['user' => $user, 'error' => ""]);
})->bind("my_profile");

$profileController->post('/save', function(Request $request) use($app) {
    $user = $app["userService"]->updateCurrentUser($request);
    return $app->render('my_profile.html.twig', ['user' => $user, 'error' => ""]);
})->bind("my_profile_save");

$profileController->post('/passwd', function(Request $request) use($app) {
    $app["userService"]->changePassword($request);
    $user = $app["userService"]->getCurrentUser();
    return $app->render('my_profile.html.twig', ['user' => $user, 'error' => ""]);
})->bind("my_profile_passwd");

$app->mount('/my_profile', $profileController);

?>