<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 Controller de campagne (sécurisé)
*/
$forumController = $app['controllers_factory'];
$forumController->before($mustBeLogged);

function getInterneCampagneNumber($campagne_id) {
	if($campagne_id == 0) {
		return null;
	} else {
		return $campagne_id;
	}
}

function getExterneCampagneNumber($campagne_id) {
	if($campagne_id == null) {
		return 0;
	} else {
		return $campagne_id;
	}
}

$forumController->get('/', function() use($app) {
	$topics = $app["sectionService"]->getAllSectionInCampagne(null);
	$is_mj = $app["campagneService"]->isMj(null);
	return $app->render('forum_campagne.html.twig', ['campagne_id' => 0, 'topics' => $topics, 'is_mj' => $is_mj]);
})->bind("forum");

$forumController->get('/{campagne_id}', function($campagne_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topics = $app["sectionService"]->getAllSectionInCampagne($campagne_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('forum_campagne.html.twig', ['campagne_id' => $campagne_id, 'topics' => $topics, 'is_mj' => $is_mj]);
})->bind("forum_campagne");

$forumController->get('/{campagne_id}/section/add', function($campagne_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$section =  $app["sectionService"]->getBlankSection($campagne_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('section_form.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => '', 'is_mj' => $is_mj]);
})->bind("section_add");

$forumController->get('/{campagne_id}/section/edit/{section_id}', function($campagne_id, $section_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$section =  $app["sectionService"]->getSection($section_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('section_form.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => '', 'is_mj' => $is_mj]);
})->bind("section_edit");

$forumController->post('/{campagne_id}/section/save', function($campagne_id, Request $request) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$sectionId = $request->get('id');
	try {
		if($sectionId == '') {
			$app["sectionService"]->createSection($request, $campagne_id);
		} else {
			$app["sectionService"]->updateSection($request);
		} 
		$campagne_id = getExterneCampagneNumber($campagne_id);
		return $app->redirect($app->path('forum_campagne', array('campagne_id' => $campagne_id)));
	} catch(Exception $e) {
		$section =  $app["sectionService"]->getFormSection($campagne_id, $request);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$campagne_id = getExterneCampagneNumber($campagne_id);
		return $app->render('section_form.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => $e->getMessage(), 'is_mj' => $is_mj]);
	}
})->bind("section_save");

$forumController->get('/{campagne_id}/topic/add/{section_id}', function($campagne_id, $section_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topic =  $app["topicService"]->getBlankTopic($section_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'error' => '', 'is_mj' => $is_mj]);
})->bind("topic_add");

$forumController->get('/{campagne_id}/topic/edit/{topic_id}', function($campagne_id, $topic_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topic =  $app["topicService"]->getTopic($topic_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'error' => '', 'is_mj' => $is_mj]);
})->bind("topic_edit");

$forumController->post('/{campagne_id}/topic/save', function($campagne_id, Request $request) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topicId = $request->get('id');
	try {
		if($topicId == '') {
			$app["topicService"]->createTopic($request);
		} else {
			$app["topicService"]->updateTopic($request);
		}
		$campagne_id = getExterneCampagneNumber($campagne_id);
		return $app->redirect($app->path('forum_campagne', array('campagne_id' => $campagne_id)));
	} catch(Exception $e) {
		$topic =  $app["topicService"]->getFormSection($campagne_id, $request);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$campagne_id = getExterneCampagneNumber($campagne_id);
		return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id,'topic' => $topic, 'error' => $e->getMessage(), 'is_mj' => $is_mj]);
	}
})->bind("topic_save");

$forumController->get('/{campagne_id}/{topic_id}', function($campagne_id, $topic_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$posts = $app["postService"]->getPostsInTopic($topic_id);
	
	$last_id = 0;
	if(count($posts) > 0) {
		$last_id = $posts[count($posts) - 1]['post_id'];
		$app["postService"]->markRead($last_id, $topic_id);
	}
	$topic = $app["topicService"]->getTopic($topic_id);
	$perso = $app['persoService']->getPersonnage(false, $campagne_id, $app['session']->get('user')['id']);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$personnages = $app['persoService']->getAllPersonnagesInCampagne($campagne_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('forum_topic.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'posts' => $posts, 
			'perso' => $perso, 'is_mj' => $is_mj, 'personnages' => $personnages]);
})->bind("topic");

$forumController->get('/{campagne_id}/post/edit/{post_id}', function($campagne_id, $post_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$post =  $app["postService"]->getPost($post_id);
	$personnages = $app['persoService']->getAllPersonnagesInCampagne($campagne_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('forum_post.html.twig', ['campagne_id' => $campagne_id, 'post' => $post, 
			'error' => '', 'is_mj' => $is_mj, 'personnages' => $personnages]);
})->bind("post_edit");

$forumController->get('/{campagne_id}/post/deletePost/{topic_id}/{post_id}', function($campagne_id, $topic_id, $post_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$post =  $app["postService"]->deletePost($post_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->redirect($app->path('topic', array('campagne_id' => $campagne_id, 'topic_id' => $topic_id)));
})->bind("post_delete");

$forumController->post('/{campagne_id}/post/save', function(Request $request, $campagne_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topicId = $request->get('topic_id');
	if ($request->get('id') == '') {
		$post_id = $app["postService"]->createPost($request);
		$app["topicService"]->updateLastPost($topicId, $post_id);
	} else {
		$app["postService"]->updatePost($request);
	} 
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->redirect($app->path('topic', array('campagne_id' => $campagne_id, 'topic_id' => $topicId)));
})->bind("post_save");


$app->mount('/forum', $forumController);

?>