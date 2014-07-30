<?php
/**
 * Controller for WYSIWYG element
 *
 * @package common
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */

$editorController = $app['controllers_factory'];
$editorController->before($mustBeLogged);


$editorController->get('/tagPerso/{campagne_id}', function($campagne_id) use ($app) {
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$allPerso = $app['persoService']->getPNJInCampagne($campagne_id,$is_mj);
	return $app->render('editor/editor_tag_perso.html.twig', ['campagne_id' => $campagne_id, 'allPerso' => $allPerso]);
})->bind("tag_perso");

$editorController->get('/tagPrivate/{campagne_id}', function($campagne_id) use ($app) {
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id,$is_mj);
	return $app->render('editor/editor_tag_private.html.twig', ['campagne_id' => $campagne_id, 'allPerso' => $allPerso]);
})->bind("tag_private");

$app->mount('/editor', $editorController);

?>
