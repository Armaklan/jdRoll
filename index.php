<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/constante/StatutCampagn.php';
require_once __DIR__.'/service/dbService.php';
require_once __DIR__.'/service/userService.php';
require_once __DIR__.'/service/campagneService.php';
require_once __DIR__.'/service/persoService.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyApplication extends Silex\Application
{
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
}

$app = new MyApplication();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/app.db',
    ),
));

$app["debug"] = true;


$mustBeLogged = function (Request $request) use ($app) {
    if (!isLog($app)) {
        return $app->redirect($app->path('login_page'));
    }
};

function isLog($app) {
    return ($app['session']->get('user') != null);
}

/*
    Définition des services
*/
$app['dbService'] = function ($app) {
    return new DbService($app['db']);
};
$app['userService'] = function ($app) {
    return new UserService($app['db'], $app['session']);
};
$app['persoService'] = function ($app) {
    return new PersoService($app['db'], $app['session']);
};
$app['campagneService'] = function ($app) {
    return new CampagneService($app['db'], $app['session'], $app['persoService']);
};

require("controller/common.php");
require("controller/profile.php");
require("controller/secure_campagn.php");
require("controller/public_campagn.php");
require("controller/subscribe.php");
require("controller/database.php");

Request::enableHttpMethodParameterOverride();
$app->run();