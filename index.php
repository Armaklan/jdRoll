<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/constante/StatutCampagn.php';
require_once __DIR__.'/service/dbService.php';
require_once __DIR__.'/service/userService.php';
require_once __DIR__.'/service/campagneService.php';
require_once __DIR__.'/service/persoService.php';
require_once __DIR__.'/service/forum/sectionsService.php';
require_once __DIR__.'/service/forum/topicsService.php';
require_once __DIR__.'/service/forum/postsService.php';
require_once __DIR__.'/service/dicerService.php';

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
$app->register(new Silex\Provider\SessionServiceProvider(array('cookie_lifetime' => 0, 'name' => "_JDROLL_SESS")));
require_once("config.php");

$app->register(new Silex\Provider\MonologServiceProvider(), array(
		'monolog.logfile' => __DIR__.'/development.log',
));

$app["debug"] = true;


$mustBeLogged = function (Request $request) use ($app) {
    if (!isLog($app)) {
    	$url =  str_replace('/', '!', $request->getUri());
        return $app->redirect($app->path('login_page', array('url' =>  $url)));
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
    return new CampagneService($app['db'], $app['session'], $app['persoService'], $app['userService']);
};
$app['sectionService'] = function ($app) {
	return new SectionService($app['db'], $app['session']);
};
$app['topicService'] = function ($app) {
	return new TopicService($app['db'], $app['session'], $app['monolog']);
};
$app['postService'] = function ($app) {
	return new PostService($app['db'], $app['session'], $app['monolog']);
};
$app['dicerService'] = function ($app) {
	return new DicerService($app['db'], $app['session']);
};

require("controller/common.php");
require("controller/profile.php");
require("controller/secure_campagn.php");
require("controller/public_campagn.php");
require("controller/subscribe.php");
require("controller/perso.php");
require("controller/database.php");
require("controller/messagerie.php");
require("controller/forum.php");

Request::enableHttpMethodParameterOverride();
$app->run();