<?php

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'jdRoll',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8',
    ),
));

?>