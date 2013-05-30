<?php

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	/*
	    Controller de campagne (sécurisé)
	*/
	$chatController = $app['controllers_factory'];
	$chatController->before($mustBeLogged);

	$chatController->get('/', function() use($app) {
	    $last_msg = $app['chatService']->getLastMsg();
	    return $app->render('chat/show.html.twig', ['msgs' => $last_msg]);
	})->bind("chat_last_msg");

	$chatController->post('/post', function(Request $request) use($app) {
		$app['chatService']->postMsg($request->get('user'), $request->get('message'));
		return "ok";
	})->bind("chat_post");
	
	$app->mount('/chat', $chatController);

?>