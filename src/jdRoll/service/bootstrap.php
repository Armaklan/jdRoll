<?php

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
use jdRoll\service\FeedbackService;
use jdRoll\service\AnnonceService;
use jdRoll\service\ThemeService;
use jdRoll\service\PostContentService;
use jdRoll\service\ThumbnailService;
use jdRoll\service\CarteService;
use jdRoll\service\NoteService;

/*
    Définition des services
*/
$app['dbService'] = function ($app) {
    return new DbService($app['db']);
};
$app['userService'] = function ($app) {
    return new UserService($app['db'], $app['session']);
};
$app['thumbnailService'] = function ($app) {
    return new ThumbnailService($app['db'], $app['session']);
};
$app['persoService'] = function ($app) {
    return new PersoService($app['db'], $app['session'], $app['thumbnailService']);
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
    return new NotificationService($app['db'], $app['monolog'], $app['userService'], $app['topicService'], $app['campagneService'], $app['mailer']);
};
$app['absenceService'] = function ($app) {
    return new AbsenceService($app['db'], $app['session']);
};
$app['resetPwdService'] = function ($app) {
    return new resetPwdService($app['db'], $app['session'],$app['messagerieService']);
};
$app['feedbackService'] = function ($app) {
    return new FeedbackService($app['db'], $app['monolog']);
};
$app['annonceService'] = function ($app) {
    return new AnnonceService($app['db'], $app['monolog']);
};
$app['themeService'] = function ($app) {
    return new ThemeService($app['db'], $app['monolog']);
};
$app['postContentService'] = function ($app) {
    return new PostContentService($app['persoService'],$app['session']);
};
$app['carteService'] = function ($app) {
    return new CarteService($app['db'], $app['session'], $app['thumbnailService']);
};
$app['noteService'] = function ($app) {
    return new NoteService($app['db']);
};

