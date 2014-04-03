<?php

use jdRoll\controller\SessionController;

$app['session.controller'] = $app->share(function() use ($app) {
    return new SessionController($app['session'], $app['monolog']);
});