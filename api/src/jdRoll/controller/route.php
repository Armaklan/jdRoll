<?php

/**
 * Session Controller Routing
 */
$main = $app['controllers_factory'];
$main->get('/', "controller.session:indexAction");
$main->get('/', "controller.session:indexAction");
$main->post('/login', "controller.session:loginAction");
$main->get('/logout', "controller.session:logoutAction");
$app->mount('/api', $main);

/**
 * User Controller Routing
 */
$user = $app['controllers_factory'];
$user->get('/', "controller.user:searchAction");
$user->get('/current', "controller.user:currentAction");
$user->get('/{id}', "controller.user:getAction");
$app->mount('/api/user', $user);

/**
 * User Controller Routing
 */
$games = $app['controllers_factory'];
$games->get('/', "controller.games:searchAction");
$app->mount('/api/games', $games);

/**
 * Absence Controller Routing
 */
$absences = $app['controllers_factory'];
$absences->get('/', "controller.absences:searchAction");
$app->mount('/api/absences', $absences);