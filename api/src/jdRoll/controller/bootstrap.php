<?php

use jdRoll\controller\SessionController;
use jdRoll\controller\UserController;
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


$app['controller.session'] = $app->share(function() use ($app) {
    return new SessionController($app['session'], $app['monolog'], $app['service.user']);
});

$app['controller.user'] = $app->share(function() use ($app) {
    return new UserController($app['session'], $app['monolog'], $app['service.user']);
});

require __DIR__.'/route.php';