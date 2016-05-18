<?php
/**
 * Include all controller file
 *
 * @package chat
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\Response;

    $mustBeLogged = function (Request $request) use ($app) {
        if (!isLog($app, $request)) {
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

    $autoLog = function (Request $request) use ($app) {
        if ($app['session']->get('user') == null) {
            $cookies = $request->cookies;
            if($cookies->has("autoLogin")) {
                $app['userService']->autoLogin($cookies->get('autoLogin'), $cookies->get('autoLoginId'));
            }
        }
    };

    function isLog($app, $request) {
        if ($app['session']->get('user') != null) {
            return true;
        }
        return false;
    }

    $app->before($autoLog);
    require("common.php");
    require("admin.php");
    require("profile.php");
    require("secure_campagn.php");
    require("public_campagn.php");
    require("notes.php");
    require("subscribe.php");
    require("perso.php");
    require("messagerie.php");
    require("notification.php");
    require("forum.php");
    require("feedback.php");
    require("resetPwd.php");
	require("editor.php");
    require("api/game.php");
    require("application.php");
    require("carte.php");
