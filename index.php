<?php

require __DIR__.'/vendor/autoload.php';

require __DIR__.'/src/jdRoll/service/dicer/dicer.php';
require __DIR__.'/src/jdRoll/service/dicer/JetElt.php';
require __DIR__.'/src/jdRoll/service/dicer/Dice.php';
require __DIR__.'/src/jdRoll/service/dicer/Group.php';
require __DIR__.'/src/jdRoll/service/dicer/StaticValue.php';

use \Silex\Application;
use \Symfony\Component\HttpFoundation\Request;

class MyApplication extends Application
{
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
}

$app = new MyApplication();


require("config.php");
require("src/jdRoll/service/bootstrap.php");
require("src/jdRoll/controller/bootstrap.php");

Request::enableHttpMethodParameterOverride();
$app['http_cache']->run();
