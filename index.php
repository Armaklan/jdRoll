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
require_once __DIR__.'/service/chatService.php';

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
$app->register(new Silex\Provider\SessionServiceProvider(array('cookie_lifetime' => 0, 'name' => "_JDROLL_SESS", 'gc_maxlifetime' => 432000)));
require_once("config.php");

$app->register(new Silex\Provider\MonologServiceProvider(), array(
		'monolog.logfile' => __DIR__.'/development.log',
));

$app["debug"] = true;

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
$app['chatService'] = function ($app) {
	return new ChatService($app['db'], $app['session']);
};



$mustBeLogged = function (Request $request) use ($app) {
	if (!isLog($app)) {
		$url =  str_replace('/', '!', $request->getUri());
		return $app->redirect($app->path('login_page', array('url' =>  $url)));
	} else {
		$app['userService']->updateLastActionTime();
	}
};

function isLog($app) {
	return ($app['session']->get('user') != null);
}



require("controller/common.php");
require("controller/profile.php");
require("controller/secure_campagn.php");
require("controller/public_campagn.php");
require("controller/subscribe.php");
require("controller/perso.php");
require("controller/database.php");
require("controller/messagerie.php");
require("controller/forum.php");
require("controller/chat.php");

Request::enableHttpMethodParameterOverride();
$app->run();