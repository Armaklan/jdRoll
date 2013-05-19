<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 Controller de campagne (sécurisé)
*/
$forumController = $app['controllers_factory'];
$forumController->before($mustBeLogged);

$forumController->get('/', function() use($app) {
	return $app->render('blank.html.twig', ['msg' => "Fonctionnalité non implémentée"]);
})->bind("forum");

$forumController->get('/{campagne_id}', function($campagne_id) use($app) {
	$topics = $app["sectionService"]->getAllSectionInCampagne($campagne_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	return $app->render('forum_campagne.html.twig', ['campagne_id' => $campagne_id, 'topics' => $topics, 'is_mj' => $is_mj]);
})->bind("forum_campagne");

$forumController->get('/{campagne_id}/section/add', function($campagne_id) use($app) {
	$section =  $app["sectionService"]->getBlankSection($campagne_id);
	return $app->render('section_form.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => '']);
})->bind("section_add");

$forumController->get('/{campagne_id}/section/edit/{section_id}', function($campagne_id, $section_id) use($app) {
	$section =  $app["sectionService"]->getSection($section_id);
	return $app->render('section_form.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => '']);
})->bind("section_edit");

$forumController->post('/{campagne_id}/section/save', function($campagne_id, Request $request) use($app) {
	$sectionId = $request->get('id');
	try {
		if($sectionId == '') {
			$app["sectionService"]->createSection($request, $campagne_id);
		} else {
			$app["sectionService"]->updateSection($request);
		} 
		return $app->redirect($app->path('forum_campagne', array('campagne_id' => $campagne_id)));
	} catch(Exception $e) {
		$section =  $app["sectionService"]->getFormSection($campagne_id, $request);
		return $app->render('section_form.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => $e->getMessage()]);
	}
})->bind("section_save");

$forumController->get('/{campagne_id}/topic/add/{section_id}', function($campagne_id, $section_id) use($app) {
	$topic =  $app["topicService"]->getBlankTopic($section_id);
	return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'error' => '']);
})->bind("topic_add");

$forumController->get('/{campagne_id}/topic/edit/{topic_id}', function($campagne_id, $topic_id) use($app) {
	$topic =  $app["topicService"]->getTopic($topic_id);
	return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'error' => '']);
})->bind("topic_edit");

$forumController->post('/{campagne_id}/topic/save', function($campagne_id, Request $request) use($app) {
	$topicId = $request->get('id');
	try {
		if($topicId == '') {
			$app["topicService"]->createTopic($request);
		} else {
			$app["topicService"]->updateTopic($request);
		}
		return $app->redirect($app->path('forum_campagne', array('campagne_id' => $campagne_id)));
	} catch(Exception $e) {
		$topic =  $app["topicService"]->getFormSection($campagne_id, $request);
		return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id,'topic' => $topic, 'error' => $e->getMessage()]);
	}
})->bind("topic_save");

$forumController->get('/{campagne_id}/{topic_id}', function($campagne_id, $topic_id) use($app) {
	$posts = $app["postService"]->getPostsInTopic($topic_id);
	
	$last_id = 0;
	if(count($posts) > 0) {
		$last_id = $posts[count($posts) - 1]['post_id'];
		$app["postService"]->markRead($last_id, $topic_id);
	}
	$topic = $app["topicService"]->getTopic($topic_id);
	$perso = $app['persoService']->getPersonnage($campagne_id, $app['session']->get('user')['id']);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	return $app->render('forum_topic.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'posts' => $posts, 'perso' => $perso, 'is_mj' => $is_mj]);
})->bind("topic");

$forumController->post('/{campagne_id}/post/save', function(Request $request, $campagne_id) use($app) {
	$topicId = $request->get('topic_id');
	if ($request->get('id') == '') {
		$post_id = $app["postService"]->createPost($request);
		$app["topicService"]->updateLastPost($topicId, $post_id);
	} else {
		$app["postService"]->updatePost($request);
	} 
	return $app->redirect($app->path('topic', array('campagne_id' => $campagne_id, 'topic_id' => $topicId)));
})->bind("post_save");


$app->mount('/forum', $forumController);

?>