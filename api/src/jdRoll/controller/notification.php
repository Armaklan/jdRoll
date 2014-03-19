<?php
/**
 * Control notification operation
 *
 * @package messagerie
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 Controller de campagne (sécurisé)
*/
$notificationController = $app['controllers_factory'];
$notificationController->before($mustBeLogged);

$notificationController->get('/', function() use($app) {
    $user_id = $app["session"]->get('user')['id'];
	$notifs = $app['notificationService']->getNotifForUser($app["session"]->get('user')['id']);
    $nbNotif = count($notifs);
    $hasNotif = ($nbNotif > 0);
	return $app->render('notification/btn.html.twig', ['notifs' => $notifs, 'has_notif' => $hasNotif, 'nb_notif' => $nbNotif]);
})->bind("notifications_btn");

$notificationController->get('/list', function() use($app) {
    $user_id = $app["session"]->get('user')['id'];
    $notifs = $app['notificationService']->getNotifForUser($app["session"]->get('user')['id']);
    $nbNotif = count($notifs);
    $hasNotif = ($nbNotif > 0);
    return $app->render('notification/list.html.twig', ['notifs' => $notifs, 'has_notif' => $hasNotif, 'nb_notif' => $nbNotif]);
})->bind("notifications");

$notificationController->get('/ajax', function() use($app) {
     $user_id = $app["session"]->get('user')['id'];
	 $notifs = $app['notificationService']->getNotifForUser($app["session"]->get('user')['id']);
     $nbNotif = count($notifs);
     $hasNotif = ($nbNotif > 0);
     $notifsList =  $app->render('notification/list.html.twig', ['notifs' => $notifs, 'has_notif' => $hasNotif, 'nb_notif' => $nbNotif]);
     $ret["content"] = $notifsList->getContent();
     $ret["nb_notif"] = $nbNotif;
     return new Response(json_encode($ret), 200, array('Content-Type' => 'application/json') );
})->bind("notifications_ajax");

$notificationController->post('/del', function(Request $request) use($app) {
        $id = $request->get('id');
	$app['notificationService']->deleteNotif($id);
        return "ok";
})->bind("notification_del");

$app->mount('/notification', $notificationController);

?>
