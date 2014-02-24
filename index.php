<?php

require __DIR__.'/vendor/autoload.php';

require __DIR__.'/src/jdRoll/service/dicer/dicer.php';
require __DIR__.'/src/jdRoll/service/dicer/JetElt.php';
require __DIR__.'/src/jdRoll/service/dicer/Dice.php';
require __DIR__.'/src/jdRoll/service/dicer/Group.php';
require __DIR__.'/src/jdRoll/service/dicer/StaticValue.php';

use \Silex\Application;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

use jdRoll\service\DbService;
use jdRoll\service\UserService;
use jdRoll\service\PersoService;
use jdRoll\service\CampagneService;
use jdRoll\service\forum\SectionService;
use jdRoll\service\forum\TopicService;
use jdRoll\service\forum\PostService;
use jdRoll\service\DicerService;
use jdRoll\service\ChatService;
use jdRoll\service\MessagerieService;
use jdRoll\service\NotificationService;
use jdRoll\service\AbsenceService;
use jdRoll\service\resetPwdService;

class MyApplication extends Application
{
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
}

$app = new MyApplication();
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/src/views',
));
require_once("config.php");

$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Silex\Provider\SessionServiceProvider(array('cookie_lifetime' => 0, 'name' => "_JDROLL_SESS", 'gc_maxlifetime' => 432000)));

// Registers Swiftmailer extension
$app->register(new \Silex\Provider\SwiftmailerServiceProvider(), array());
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


$app->register(new \Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__.'/cache/',
));

$app->register(new \Silex\Provider\MonologServiceProvider(), array(
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
$app['messagerieService'] = function ($app) {
    return new MessagerieService($app['db'], $app['session'], $app['monolog'], $app['userService'], $app['mailer']);
};
$app['notificationService'] = function ($app) {
    return new NotificationService($app['db'], $app['monolog'], $app['userService'], $app['topicService'], $app['campagneService']);
};
$app['absenceService'] = function ($app) {
    return new AbsenceService($app['db'], $app['session']);
};
$app['resetPwdService'] = function ($app) {
    return new resetPwdService($app['db'], $app['session'],$app['messagerieService']);
};

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

require("src/jdRoll/controller/common.php");
require("src/jdRoll/controller/profile.php");
require("src/jdRoll/controller/secure_campagn.php");
require("src/jdRoll/controller/public_campagn.php");
require("src/jdRoll/controller/subscribe.php");
require("src/jdRoll/controller/perso.php");
require("src/jdRoll/controller/messagerie.php");
require("src/jdRoll/controller/notification.php");
require("src/jdRoll/controller/forum.php");
require("src/jdRoll/controller/chat.php");
require("src/jdRoll/controller/resetPwd.php");

Request::enableHttpMethodParameterOverride();
//$app['http_cache']->run();
$app->run();
