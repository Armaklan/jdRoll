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
use Symfony\Component\HttpFoundation\JsonResponse;

/*
    Controller de chat
*/
$adminController = $app['controllers_factory'];

$adminController->get('/annonces', function() use($app) {
    return $app->render('admin/annonces.html.twig', []);
})->bind("admin_annonces");

$adminController->get('/annonces/list', function() use($app) {
    $data = $app['annonceService']->all();
    return new JsonResponse($data, 200);
});

$adminController->put('/annonces', function(Request $request) use($app) {
    $data = json_decode($request->getContent());
    $data = $app['annonceService']->save($data);
    return new JsonResponse($data, 200);
});

$adminController->post('/annonces', function(Request $request) use($app) {
    $data = json_decode($request->getContent());
    $data = $app['annonceService']->add($data);
    return new JsonResponse($data, 200);
});



$app->mount('/admin', $adminController);

?>




