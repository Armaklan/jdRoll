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
            $content = $app['noteService']->getNote($campagne_id, $user_id);
            return $app->render('notes/modal.html.twig', ['campagne_id' => $campagne_id, 'content_notes' => $content]);
        })->bind("notes_popup");

$notesController->get('/{campagne_id}/content', function($campagne_id) use($app) {
            $user_id = $app['session']->get('user')['id'];
            $content = $app['noteService']->getNote($campagne_id, $user_id);
            return new JsonResponse($content);
        });

$notesController->post('/{campagne_id}', function($campagne_id, Request $request) use($app) {
            $note = json_decode($request->getContent());
            $user_id = $app['session']->get('user')['id'];
            $app['noteService']->updateNote($campagne_id, $user_id, $note);
            return new JsonResponse($content);
        });

$notesController->delete('/{campagne_id}', function($campagne_id, Request $request) use($app) {
            $note = json_decode($request->getContent());
            $user_id = $app['session']->get('user')['id'];
            $app['noteService']->removeNote($campagne_id, $user_id, $note);
            return new JsonResponse("", 200);
        });

$app->mount('/notes', $notesController);
?>
