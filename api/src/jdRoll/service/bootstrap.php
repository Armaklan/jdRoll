<?php

use jdRoll\service\AbsenceService;
use jdRoll\service\CampagneService;
use jdRoll\service\ChatService;
use jdRoll\service\DbService;
use jdRoll\service\DicerService;
use jdRoll\service\MessagerieService;
use jdRoll\service\NotificationService;
use jdRoll\service\PersoService;
use jdRoll\service\UserService;
use jdRoll\service\forum\PostService;
use jdRoll\service\forum\SectionService;
use jdRoll\service\forum\TopicService;
use jdRoll\service\resetPwdService;

/*
    Définition des services
*/
$app['service.user'] = function ($app) {
    return new UserService($app['db'], $app['session'], $app['monolog']);
};
$app['service.perso'] = function ($app) {
    return new PersoService($app['db'], $app['session']);
};
$app['service.campagne'] = function ($app) {
    return new CampagneService($app['db'], $app['session'], $app['service.perso'], $app['service.user']);
};
$app['service.forum.section'] = function ($app) {
    return new SectionService($app['db'], $app['session']);
};
$app['service.forum.topic'] = function ($app) {
    return new TopicService($app['db'], $app['session'], $app['monolog']);
};
$app['service.forum.post'] = function ($app) {
    return new PostService($app['db'], $app['session'], $app['monolog']);
};
$app['service.dicer'] = function ($app) {
    return new DicerService($app['db'], $app['session']);
};
$app['service.chat'] = function ($app) {
    return new ChatService($app['db'], $app['session']);
};
$app['service.messagerie'] = function ($app) {
    return new MessagerieService($app['db'], $app['session'], $app['monolog'], $app['service.user'], $app['mailer']);
};
$app['service.notification'] = function ($app) {
    return new NotificationService($app['db'], $app['monolog'], $app['service.user'], $app['service.forum.topic'], $app['service.campagne']);
};
$app['service.absence'] = function ($app) {
    return new AbsenceService($app['db'], $app['session']);
};
$app['service.password.reset'] = function ($app) {
    return new resetPwdService($app['db'], $app['session'],$app['service.messagerie']);
};