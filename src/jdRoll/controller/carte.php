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
$carteController = $app['controllers_factory'];
$carteController->before($mustBeLogged);

$carteController->get('/{campagne_id}/display/{carte_id}', function($campagne_id, $carte_id) use($app) {
    $carte = $app['carteService']->getCarte($carte_id);
    $is_mj = $app["campagneService"]->isMj($campagne_id);
    return $app->render('carte/display.html.twig', ['campagne_id' => $campagne_id, 'is_mj'=>$is_mj, 'carte' => $carte, 'carte_json'=>json_encode($carte)]);
})->bind("carte_display");


$app->mount('/carte', $carteController);
