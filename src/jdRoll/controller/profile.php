<?php
/**
 * User profile controller
 *
 * @package profile
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
    Controller du profile
*/
$profileController = $app['controllers_factory'];
$profileController->before($mustBeLogged);

$profileController->get('/', function() use($app) {
    $user = $app["userService"]->getCurrentUser();
    return $app->render('my_profile.html.twig', ['user' => $user, 'error' => ""]);
})->bind("my_profile");

$profileController->post('/save', function(Request $request) use($app) {
    $user = $app["userService"]->updateCurrentUser($request);
    return $app->render('my_profile.html.twig', ['user' => $user, 'error' => ""]);
})->bind("my_profile_save");

$profileController->post('/passwd', function(Request $request) use($app) {
    $app["userService"]->changePassword($request);
    $user = $app["userService"]->getCurrentUser();
    return $app->render('my_profile.html.twig', ['user' => $user, 'error' => ""]);
})->bind("my_profile_passwd");

$profileController->get('/absences', function() use($app) {
    $absence_form = $app["absenceService"]->getBlankForm($app['session']->get('user')['id']);
    $absences = $app["absenceService"]->getAllAbsence($app['session']->get('user')['id']);
    return $app->render('absences.html.twig', ['absences' => $absences, 'absence_form' => $absence_form, 'error' => ""]);
})->bind("abs");

$profileController->get('/absences/edit/{id}', function($id) use($app) {
    $absence_form = $app["absenceService"]->getAbsence($id);
    $absences = $app["absenceService"]->getAllAbsence($app['session']->get('user')['id']);
    return $app->render('absences.html.twig', ['absences' => $absences,'absence_form' => $absence_form, 'error' => ""]);
})->bind("abs_edit");

$profileController->get('/absences/remove/{id}', function($id) use($app) {
    $app["absenceService"]->deleteAbsence($id);
    return $app->redirect($app->path('abs'));
})->bind("abs_remove");

$profileController->post('/absences/save', function(Request $request) use($app) {
    if ($request->get('id') == 0) {
        $app["absenceService"]->insertAbsence($request);
    } else {
        $app["absenceService"]->updateAbsence($request);
    }
    return $app->redirect($app->path('abs'));
})->bind("abs_save");

$profileController->get('/config', function() use($app) {
    return $app->render('config.html.twig', []);
})->bind("user_conf");

$profileController->get('/stat', function() use($app) {
    $user = $app["userService"]->getCurrentUser();
    $data = $app['postService']->getStatPost($user);
    return $app->render('mystat.html.twig', ['data' => $data]);
})->bind("user_stat");

$app->mount('/my_profile', $profileController);

?>
