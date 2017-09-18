<?php

use Monolog\Logger;
use \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;


//Constantes
define('FOLDER_FILES', __DIR__.'/files/');
define('FOLDER_BASE', __DIR__);

/**
 * Config provider
 */
$app->register(
        new GeckoPackages\Silex\Services\Config\ConfigServiceProvider(),
        array(
            'config.dir' => __DIR__,
            'config.format' => 'config.yml',
            'config.env' => 'prod'
        )
);

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/src/views',
));
$app->register(new \Silex\Provider\HttpFragmentServiceProvider());


/**
 * Database
 */
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['config']['prd']['database']
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
$app['session.storage.handler'] = function () use ($app) {
    return new Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler(
            $app['db']->getWrappedConnection(),
            $app['session.db_options'],
            $app['session.storage.options']
    );
};

/**
 * General configuration
 */
$app["debug"] = $app['config']['prd']['general']['debug'];


$app->register(new \Silex\Provider\RoutingServiceProvider());

// Registers Swiftmailer extension
$app->register(new \Silex\Provider\SwiftmailerServiceProvider(), array());
$app['mailer'] = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());


$app->register(new \Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__.'/cache/',
));

$app->register(new \Silex\Provider\MonologServiceProvider(), array(
        'monolog.logfile' => __DIR__.'/development.log',
));



if($app['config']['prd']['log']['level'] == "ERROR") {
    $app['monolog.level'] = Logger::ERROR;
} elseif($app['config']['prd']['log']['level'] == "INFO") {
    $app['monolog.level'] = Logger::INFO;
} else {
    $app['monolog.level'] = Logger::DEBUG;
}
