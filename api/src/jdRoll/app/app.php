<?php

require_once __DIR__.'/bootstrap.php';

use Silex\Application;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;

$app = new Application();

/**
 * Config provider
 */
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__.'/../../../config.yml'));

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
            $app['db']->getWrappedConnection(),
            $app['session.db_options'],
            $app['session.storage.options']
    );
});


/**
 * Mailer
 */
$app->register(new \Silex\Provider\SwiftmailerServiceProvider(), array());
$app['mailer'] = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());


/**
 * Cache
 */
$app->register(new \Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => $baseAppDir.'/cache/',
));


/**
 * Logger
 */
$app->register(new \Silex\Provider\MonologServiceProvider(), array(
        'monolog.logfile' => $baseAppDir.'/logs/development.log',
));

/**
 * Controller provider
 */
$app->register(new Silex\Provider\ServiceControllerServiceProvider());


/**
 * Error handler
 */
$app->error(function (\Exception $e, $code) use($app) {
    try {
        $app['monolog']->addError("Error handling : " . $e->getMessage());
    } catch (\Exception $e) {
        // Nothing => No error log if monologer is out
    }
    return new JsonResponse(array("error" => $e->getMessage()), $e->getCode());    
});

ErrorHandler::register(false);

/**
 * General configuration
 */
$app["debug"] = $app['config']['general']['debug'];


require __DIR__.'/../service/bootstrap.php';
require __DIR__.'/../controller/bootstrap.php';

return $app;