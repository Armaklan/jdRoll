<?php
/**
 * Control top heading carte Operation
 *
 * @package carte
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/*
 Controller de carte (sécurisé)
*/
$applicationController = $app['controllers_factory'];
$applicationController->before($mustBeLogged);

$applicationController->get('/{campagne_id}/', function($campagne_id) use($app) {
    $is_mj = $app["campagneService"]->isMj($campagne_id);
    return $app->render('application/index.html.twig', ['campagne_id' => $campagne_id, 'is_mj'=>$is_mj]);
})->bind("ng_app");

$app->mount('/app', $applicationController);
