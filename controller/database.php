<?php

/*
    Controller de base de données
*/
$databaseController = $app['controllers_factory'];
$databaseController->before($mustBeLogged);
$databaseController->get('/init', function() use($app) {
	$dbService = $app['dbService'];
	$dbService->init();
	return $app->redirect($app->path('homepage'));
});

$databaseController->get('/drop', function() use($app) {
	$dbService = $app['dbService'];
	$dbService->drop();
	return $app->redirect($app->path('homepage'));
});

$databaseController->get('/reinit', function() use($app) {
	$dbService = $app['dbService'];
	$dbService->reInit();
	return $app->redirect($app->path('homepage'));
});
$app->mount('/db', $databaseController);

?>