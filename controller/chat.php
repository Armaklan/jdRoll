<?php
/**
 * Control chat operation
 *
 * @package chat
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	/*
	    Controller de chat
	*/
	$chatController = $app['controllers_factory'];

	$chatController->get('/', function(Request $request) use($app) {

		$isFirstLoad = $request->get('isFirst');
		$reinit = 0;
		$lastId = $request->get('lastId');
		$deletedIds = '';
		if($isFirstLoad == "true")
		{

			$last_msg = $app['chatService']->getLastMsg(0);
			if(count($last_msg) > 0)
			$lastId = $last_msg[count($last_msg)-1]['id'];


		}
		else
		{

			 $last_msg = $app['chatService']->getLastMsg($lastId);

			 if(count($last_msg) > 0)
			 {
				$lastId = $last_msg[count($last_msg)-1]['id'];

			 }
			 $deletedIds = $app['chatService']->getDeletedMessge();

		}

		$isAdmin = $app['campagneService']->isAdmin();
		return $app->render('chat/show.html.twig', ['msgs' => $last_msg,'isAdmin' => $isAdmin,'isFirstLoad' => $isFirstLoad, 'lastId' => $lastId, 'deletedIds' => $deletedIds]);
	})->bind("chat_last_msg");

	$chatController->get('/last', function(Request $request) use($app) {
		$deletedIds = [];
		$lastId = $request->get('lastId');
		if($lastId == 0)
		{
			$last_msg = $app['chatService']->getLastMsg(0);
			if(count($last_msg) > 0)
				$lastId = $last_msg[count($last_msg)-1]['id'];
		}
		else
		{

			 $last_msg = $app['chatService']->getLastMsg($lastId);
			 if(count($last_msg) > 0)
			 {
				$lastId = $last_msg[count($last_msg)-1]['id'];

			 }
			 $deletedIds = $app['chatService']->getDeletedMessge();
		}

		$resp = [];
		$resp['last_msg'] = $last_msg;
		$resp['last_id'] = $lastId;
		$resp['deleted'] = $deletedIds;

		return new Response(json_encode($resp), 200, array('Content-Type' => 'application/json') );
	});

	$chatController->post('/remove', function(Request $request) use($app) {
		$app['chatService']->deleteMsg($request->get('msgId'));
		return "ok";
	})->bind("chat_msg_remove")->before($mustBeAdmin);

	$chatController->post('/removelast', function(Request $request) use($app) {

		$app['chatService']->deleteLastMsg($request->get('nbToDelete'));
		return "ok";
	})->bind("chat_msg_remove_last")->before($mustBeAdmin);


	$chatController->get('/users', function() use($app) {
		$connected_users = $app['userService']->getConnected();
		return $app->render('chat/in_line.html.twig', ['connected_users' => $connected_users]);
	})->bind("chat_in_line");

	$chatController->post('/post', function(Request $request) use($app) {
		$app['chatService']->postMsg($request->get('user'), $request->get('message'));
		return "ok";
	})->bind("chat_post")->before($mustBeLogged);

    $chatController->get('/post/{user}/{message}', function($user, $message) use($app) {
		$app['chatService']->postMsg($user, $message);
		return "ok";
	})->bind("chat_post_get")->before($mustBeLogged);

	$app->mount('/chat', $chatController);

?>
