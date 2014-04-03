<?php

use jdRoll\controller\SessionController;

$app['controller.session'] = $app->share(function() use ($app) {
    return new SessionController($app['session'], $app['monolog'], $app['service.user']);
});

require __DIR__.'/route.php';