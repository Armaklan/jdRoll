<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 Controller de campagne (sécurisé)
*/
$messagerieController = $app['controllers_factory'];
$messagerieController->before($mustBeLogged);


$messagerieController->get('/', function() use($app) {
	$messages = $app['messagerieService']->getReceiveMessages();
	$sentMessages = $app['messagerieService']->getSendMessages();
	return $app->render('messagerie/message_list.html.twig', ['error' => '', 'messages' => $messages, 'sent_messages' => $sentMessages]);
})->bind("messagerie");

$messagerieController->get('/unread', function() use($app) {
	$nbMsg = $app['messagerieService']->getNbNewMessages();
	$color = "";
	if($nbMsg > 0) {
		$color = " style='color: #FCFFA6' ";
	}
	return "<i class='icon-envelope'></i> <span " . $color . ">Messagerie (" . $nbMsg . ")</span></a>" ;
})->bind("messagerie_unread");

$messagerieController->get('/view/{id}', function($id) use($app) {
	$app['messagerieService']->markRead($id);
	$message = $app['messagerieService']->getMessage($id);
	return $app->render('messagerie/message_detail.html.twig', ['error' => '', 'message' => $message]);
})->bind("messagerie_detail");

$messagerieController->get('/delete/{id}', function($id) use($app) {
	$app['messagerieService']->markDelete($id);
	return $app->redirect($app->path('messagerie'));
})->bind("messagerie_delete");

$messagerieController->get('/delete_my/{id}', function($id) use($app) {
	$app['messagerieService']->markDeleteMyMsg($id);
	return $app->redirect($app->path('messagerie'));
})->bind("messagerie_delete_my");

$messagerieController->get('/new', function() use($app) {
	$message = $app['messagerieService']->getBlankMessage();
	$users = $app['userService']->getUsernamesList();
	return $app->render('messagerie/message_form.html.twig', ['error' => '', 'message' => $message, 'list_username' => $users]);
})->bind("messagerie_new");

$messagerieController->get('/new_to/{username}', function($username) use($app) {
	$message = $app['messagerieService']->getBlankMessage();
	$message['to_usernames'] = "'" . $username . "'";
	$users = $app['userService']->getUsernamesList();
	return $app->render('messagerie/message_form.html.twig', ['error' => '', 'message' => $message, 'list_username' => $users]);
})->bind("messagerie_new_to");

$messagerieController->post('/send', function(Request $request) use($app) {
	$message = $app['messagerieService']->sendMessage($request);
	return $app->redirect($app->path('messagerie'));
})->bind("messagerie_send");

$app->mount('/messagerie', $messagerieController);

?>