<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/constante/StatutCampagn.php';
require __DIR__.'/service/dbService.php';
require __DIR__.'/service/userService.php';
require __DIR__.'/service/campagneService.php';
require __DIR__.'/service/persoService.php';
require __DIR__.'/service/forum/sectionsService.php';
require __DIR__.'/service/forum/topicsService.php';
require __DIR__.'/service/forum/postsService.php';
require __DIR__.'/service/absenceService.php';
require __DIR__.'/service/dicerService.php';
require __DIR__.'/service/chatService.php';
require __DIR__.'/service/messagerieService.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class MyApplication extends Silex\Application
{
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
}



$app = new MyApplication();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
require_once("config.php");

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider(array('cookie_lifetime' => 0, 'name' => "_JDROLL_SESS", 'gc_maxlifetime' => 432000)));
// Registers Swiftmailer extension

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array());
$app['mailer'] = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());


$app['session.db_options'] = array(
		'db_table'      => 'session',
		'db_id_col'     => 'session_id',
		'db_data_col'   => 'session_value',
		'db_time_col'   => 'session_time',
);

$app['session.storage.handler'] = $app->share(function () use ($app) {
	return new PdoSessionHandler(
			$app['db']->getWrappedConnection(),
			$app['session.db_options'],
			$app['session.storage.options']
	);
});




$app->register(new Silex\Provider\MonologServiceProvider(), array(
		'monolog.logfile' => __DIR__.'/development.log',
));

$app["debug"] = false;

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
$app['messagerieService'] = function ($app) {
	return new MessagerieService($app['db'], $app['session'], $app['monolog'], $app['userService'], $app['mailer']);
};
$app['absenceService'] = function ($app) {
	return new AbsenceService($app['db'], $app['session']);
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