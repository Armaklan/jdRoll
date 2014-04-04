<?php


/**
 * Session Controller Routing
 */
$app->get('/', "controller.session:indexAction");
$app->get('/', "controller.session:indexAction");
$app->post('/login', "controller.session:loginAction");
$app->get('/logout', "controller.session:logoutAction");

/**
 * User Controller Routing
 */
$app->get('/user', "controller.user:currentAction");