<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
    Page globale (Index, Authentification, ...)
*/
$commonController = $app['controllers_factory'];

$commonController->get('/', function() use ($app) {
    $campagnes = $app['campagneService']->getLastCampagne();
    $last_users = $app['userService']->getLastSubscribe();
    return $app->render('home.html.twig', ['campagnes' => $campagnes, 'last_users' => $last_users]);
})->bind("homepage");

$commonController->get('/login', function() use($app) {
    return $app->render('login.html.twig', ['error' => ""]);
})->bind("login_page");

$commonController->post('/login', function(Request $request) use($app) {

	$login = $request->get('login');
	$password = $request->get('pass');

    try {
        $app["userService"]->login($login, $password);
    } catch (Exception $e) {
        return $app->render('login.html.twig', ['error' => $e->getMessage()]);
    }

    return $app->redirect($app->path('homepage'));

})->bind("login_connect");

$commonController->get('/profile/{username}', function($username) use($app) {
    $user = $app["userService"]->getByUsername($username);
    return $app->render('profile.html.twig', ['error' => "", 'user' => $user]);
})->bind("profile");

$commonController->get('/about', function() use($app) {
	return $app->render('about.html.twig');
})->bind("about");

$commonController->get('/chat', function() use($app) {
	return $app->render('tchat.html.twig');
})->bind("chat");


$commonController->get('/logout', function(Request $request) use($app) {
    $app["userService"]->logout();
    return $app->redirect($app->path('homepage'));
})->bind("logout");

$commonController->get('/sidebar/std', function() use ($app) {
    $campagnes = $app['campagneService']->getAllCampagne();
    $pjCampagnes = $app['campagneService']->getMyActivePjCampagnes();
    $mjCampagnes = $app['campagneService']->getMyActiveMjCampagnes();
    return $app->render('sidebar_std.html.twig', ['active_campagnes' => $mjCampagnes, 'active_pj_campagnes' => $pjCampagnes]);
})->bind("sidebar_std");

$commonController->get('/sidebar/std_large', function() use ($app) {
	$campagnes = $app['campagneService']->getAllCampagne();
	$pjCampagnes = $app['campagneService']->getMyActivePjCampagnes();
	$mjCampagnes = $app['campagneService']->getMyActiveMjCampagnes();
	return $app->render('sidebar_std_large.html.twig', ['active_campagnes' => $mjCampagnes, 'active_pj_campagnes' => $pjCampagnes]);
})->bind("sidebar_std_large");

$commonController->post('/upload', function(Request $request) use ($app) {
	$file = $request->files->get("uploadFile");
	$filename = $request->get('filename');
	if($filename == null || $filename == "") {
		$filename = $file->getClientOriginalName();
	}
	$file->move(__DIR__.'/../files', $filename);
	return $app->path('homepage') . "files/" . $filename;
})->bind("upload");


$app->mount('/', $commonController);

?>