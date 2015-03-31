<?php
/**
 * Control secured campagne operation
 *
 * @package secure_campagn
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/*
  Controller de campagne (sécurisé)
 */
$notesController = $app['controllers_factory'];
$notesController->before($mustBeLogged);

$notesController->get('/{campagne_id}/view', function($campagne_id) use($app) {
            $user_id = $app['session']->get('user')['id'];
            $content = $app['campagneService']->getNote($campagne_id, $user_id);
            return $app->render('notes/modal.html.twig', ['campagne_id' => $campagne_id, 'content_notes' => $content]);
        })->bind("notes_popup");

$notesController->get('/{campagne_id}/content', function($campagne_id) use($app) {
            $user_id = $app['session']->get('user')['id'];
            $content = $app['campagneService']->getNote($campagne_id, $user_id);
            return new JsonResponse($content);
        })->bind("notes_content");

$notesController->post('/{campagne_id}', function($campagne_id, Request $request) use($app) {
            $content = json_decode($request->getContent())->content;
            $user_id = $app['session']->get('user')['id'];
            $app['campagneService']->updateNote($campagne_id, $user_id, $content);
            return new JsonResponse($content);
        })->bind("notes_update");


$app->mount('/notes', $notesController);
?>
