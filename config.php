<?php

use Monolog\Logger;
use \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;


//Constantes
define('FOLDER_FILES', __DIR__.'/files/');

/**
 * Config provider
 */
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__.'/config.yml'));
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/src/views',
));


/**
 * Database
 */
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['config']['database']
));


/**
 * Security
 */
$app->register(new \Silex\Provider\SessionServiceProvider(array('cookie_lifetime' => 0, 'name' => "_JDROLL_SESS", 'gc_maxlifetime' => 432000)));
$app['session.db_options'] = array(
        'db_table'      => 'session',
        'db_id_col'     => 'session_id',
        'db_data_col'   => 'session_value',
        'db_time_col'   => 'session_time',
);
$app['session.storage.handler'] = $app->share(function () use ($app) {
    return new Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler(
            $app['db']->
                getWrappedConnection(),
            $app['session.db_options'],
            $app['session.storage.options']
    );
});

/**
 * General configuration
 */
$app["debug"] = $app['config']['general']['debug'];


$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Silex\Provider\SessionServiceProvider(array('cookie_lifetime' => 0, 'name' => "_JDROLL_SESS", 'gc_maxlifetime' => 432000)));

// Registers Swiftmailer extension
$app->register(new \Silex\Provider\SwiftmailerServiceProvider(), array());
$app['mailer'] = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());


$app->register(new \Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__.'/cache/',
));

$app->register(new \Silex\Provider\MonologServiceProvider(), array(
        'monolog.logfile' => __DIR__.'/development.log',
));



if($app['config']['log']['level'] == "ERROR") {
    $app['monolog.level'] = Logger::ERROR;
} elseif($app['config']['log']['level'] == "INFO") {
    $app['monolog.level'] = Logger::INFO;
} else {
    $app['monolog.level'] = Logger::DEBUG;
}
