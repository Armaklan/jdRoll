<?php
/**
 * Control private message operation
 *
 * @package messagerie
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;

/*
 Controller de campagne (sécurisé)
*/
$messagerieController = $app['controllers_factory'];
$messagerieController->before($mustBeLogged);


$messagerieController->get('/', function() use($app) {
	$messages = $app['messagerieService']->getReceiveMessages();
	return $app->render('messagerie/message_list.html.twig', ['error' => '', 'messages' => $messages]);
})->bind("messagerie");

$messagerieController->get('/sent', function() use($app) {
	$sentMessages = $app['messagerieService']->getSendMessages();
	return $app->render('messagerie/message_list_sent.html.twig', ['error' => '', 'sent_messages' => $sentMessages]);
})->bind("messagerie_sent");

$messagerieController->get('/view/{id}', function($id) use($app) {
	$app['messagerieService']->markRead($id);
	$message = $app['messagerieService']->getMessage($id);
	$users = $app['userService']->getUsernamesList();
	return $app->render('messagerie/message_detail.html.twig', ['error' => '', 'message' => $message, 'list_username' => $users]);
})->bind("messagerie_detail");

$messagerieController->get('/delete/{id}', function($id) use($app) {
	$app['messagerieService']->markDelete($id);
	return $app->redirect($app->path('messagerie'));
})->bind("messagerie_delete");

$messagerieController->post('/delete_select', function(Request $request) use($app) {
    $delId = $request->get('del_id');
    foreach ($delId as $id) {
        $app['messagerieService']->markDelete($id);
    }
	return $app->redirect($app->path('messagerie'));
})->bind("messagerie_delete_select");

$messagerieController->post('/delete_my_select', function(Request $request) use($app) {
        $delId = $request->get('del_id');
        foreach ($delId as $id) {
            $app['messagerieService']->markDeleteMyMsg($id);
        }
	return $app->redirect($app->path('messagerie_sent'));
})->bind("messagerie_delete_my_select");

$messagerieController->get('/delete_my/{id}', function($id) use($app) {
	$app['messagerieService']->markDeleteMyMsg($id);
	return $app->redirect($app->path('messagerie_sent'));
})->bind("messagerie_delete_my");

$messagerieController->get('/new', function() use($app) {
	$message = $app['messagerieService']->getBlankMessage();
	$users = $app['userService']->getUsernamesList();
	return $app->render('messagerie/message_form.html.twig', ['error' => '', 'message' => $message, 'list_username' => $users]);
})->bind("messagerie_new");

$messagerieController->get('/new_to/{username}', function($username) use($app) {
	$message = $app['messagerieService']->getBlankMessage();
	$message['to_usernames'] = $username;
	$users = $app['userService']->getUsernamesList();
	return $app->render('messagerie/message_form.html.twig', ['error' => '', 'message' => $message, 'list_username' => $users]);
})->bind("messagerie_new_to");

$messagerieController->post('/send', function(Request $request) use($app) {
        try {
            $message = $app['messagerieService']->sendMessage($request);
        		$destinataires = json_decode($request->get('to_usernames'));
				$app['notificationService']->alertUserForMp($app['session']->get('user')['login'], $destinataires, 
						$request->get('title'), $app->path('messagerie'));
            return $app->redirect($app->path('messagerie'));
        } catch (Exception $e) {
            $message = $app['messagerieService']->getFormMessage($request);
            $users = $app['userService']->getUsernamesList();
            return $app->render('messagerie/message_form.html.twig', ['error' => $e->getMessage(), 'message' => $message, 'list_username' => $users]);
        }
})->bind("messagerie_send");

$app->mount('/messagerie', $messagerieController);

?>
