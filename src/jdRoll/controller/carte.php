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

$carteController->get('/list/{campagne_id}', function($campagne_id) use($app) {
    $is_mj = $app["campagneService"]->isMj($campagne_id);
    return new JsonResponse($app['carteService']->getAllCartes($campagne_id, $is_mj));
})->bind("carte_list_json");

$carteController->get('/{carte_id}', function($carte_id) use($app) {
    return new JsonResponse($app['carteService']->getCarte($carte_id), 200);
})->bind("carte_json");

$carteController->get('/delete/{carte_id}', function($carte_id) use($app) {
    return new JsonResponse($app['carteService']->deleteCarte($carte_id), 200);
})->bind("carte_delete");

$carteController->post('/save', function(Request $request) use($app) {
    $data = json_decode($request->getContent(), true);
    return new JsonResponse($app['carteService']->saveCarte($data), 200);
})->bind("carte_save");

$app->mount('/carte', $carteController);
