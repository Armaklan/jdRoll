<?php
/**
 * Control feedback module
 *
 * @package chat
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\JsonResponse;

	$apiGameController = $app['controllers_factory'];

   $apiGameController->get('/', function() use($app) {
     $user = $app['session']->get('user');
     $app['monolog']->AddInfo('get games for user : ' . $user['id']);
     $games = $app["campagneService"]->findForUser($user);
     return new JsonResponse($games, 200);
	})->bind("api_game_get")->before($mustBeLogged);

	$app->mount('/api/game', $apiGameController);

?>
