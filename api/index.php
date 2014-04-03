<?php


$app = require_once __DIR__.'/src/jdRoll/app/app.php';

use \Symfony\Component\HttpFoundation\Request;
$mustBeLogged = function (Request $request) use ($app) {
    if (!isLog($app)) {
        $url =  str_replace('/', '!', $request->getUri());
        return $app->redirect($app->path('login_page', array('url' =>  $url)));
    } else {
        $app['userService']->updateLastActionTime();
    }
};

$mustBeAdmin = function () use ($app) {
    if (!$app['campagneService']->isAdmin($app['session']->get('user'))) {
        return $app->redirect($app->path('homepage'));
    }
};

function isLog($app) {
    return ($app['session']->get('user') != null);
}

//$app['http_cache']->run();
$app->run();
