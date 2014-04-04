<?php

use jdRoll\controller\SessionController;
use jdRoll\controller\UserController;

$app['controller.session'] = $app->share(function() use ($app) {
    return new SessionController($app['session'], $app['monolog'], $app['service.user']);
});

$app['controller.user'] = $app->share(function() use ($app) {
    return new UserController($app['session'], $app['monolog'], $app['service.user']);
});

require __DIR__.'/route.php';