<?php
/**
 * Control top heading forum Operation
 *
 * @package forum
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 Controller de campagne (sécurisé)
*/
$forumController = $app['controllers_factory'];
$forumController->before($mustBeLogged);

function replace_hide($text)
{

$ret = preg_replace_callback('#\[hide(?:=(.*?))?\]((?:(?>[^\[]*)|(?R)|\[)*)\[/hide\]#is',
				function ($matches) {

					$txt = '';
					$m = '';
					if($matches[1] != '')
						$txt = $matches[1];
					else
						$txt = 'Informations masquées';

					if(strpos($matches[2],"[/hide]"))
						$m = replace_hide($matches[2]);
					else
						$m = $matches[2];

					return '<div><a href="javascript:void()" onclick="if (this.parentNode.getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'\'; } else { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'none\'; }"><u>' . $txt . '</u></a><div style="display:none">' . $m . '</div></div>';;

				},
				$text
			);


	return $ret;
}

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
	$isAdmin = $app["campagneService"]->isAdmin();
	return $app->render('forum_campagne.html.twig', ['absences' => array(), 'campagne_id' => 0, 'topics' => $topics, 'is_mj' => $is_mj, 'error' => '','isAdmin' => $isAdmin]);
})->bind("forum");

$forumController->get('/{campagne_id}', function($campagne_id) use($app) {
    $user_id = $app['session']->get('user')['id'];
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topics = $app["sectionService"]->getAllSectionInCampagne($campagne_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);

	$campagne_id = getExterneCampagneNumber($campagne_id);
    $absences = $app["absenceService"]->getFutureAbsenceInCampagn($campagne_id);
	$waitingUsers = $app["campagneService"]->getParticipantByStatus($campagne_id,0);
    $isFavoris = $app["campagneService"]->isFavoris($campagne_id, $user_id);
	return $app->render('forum_campagne.html.twig', ['absences' => $absences, 'is_favoris' => $isFavoris, 'campagne_id' => $campagne_id, 'topics' => $topics, 'is_mj' => $is_mj, 'error' => '','waitingUsers' => $waitingUsers]);
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
	$sections =  $app["sectionService"]->getSections($campagne_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$allPerso = $app['persoService']->getPersonnagesInCampagneLinkTopic($campagne_id, 0);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'error' => '',
			 'sections' => $sections, 'is_mj' => $is_mj, 'persos' => $allPerso]);
})->bind("topic_add");

$forumController->get('/{campagne_id}/topic/edit/{topic_id}', function($campagne_id, $topic_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topic =  $app["topicService"]->getTopic($topic_id);
	$is_mj = $app["campagneService"]->isMj($campagne_id);
	$sections =  $app["sectionService"]->getSections($campagne_id);
	$allPerso = $app['persoService']->getPersonnagesInCampagneLinkTopic($campagne_id, $topic_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'error' => '',
			'sections' => $sections, 'is_mj' => $is_mj, 'persos' => $allPerso]);
})->bind("topic_edit");

$forumController->post('/{campagne_id}/topic/save', function($campagne_id, Request $request) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topicId = $request->get('id');
	try {
		if($topicId == '') {
			$topicId = $app["topicService"]->createTopic($request);
		} else {
			$app["topicService"]->updateTopic($request);
		}
		if($request->get('is_private') != 0) {
			$app["topicService"]->updateCanRead($topicId, $request);
		}
		$campagne_id = getExterneCampagneNumber($campagne_id);
		return $app->redirect($app->path('forum_campagne', array('campagne_id' => $campagne_id)));
	} catch(Exception $e) {
		$topic =  $app["topicService"]->getFormTopic($request);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$campagne_id = getExterneCampagneNumber($campagne_id);
		$allPerso = $app['persoService']->getPersonnagesInCampagneLinkTopic($campagne_id, $topic_id);
		return $app->render('topic_form.html.twig', ['campagne_id' => $campagne_id,'topic' => $topic, 'error' => $e->getMessage(), 'is_mj' => $is_mj, 'persos' => $allPerso]);
	}
})->bind("topic_save");

$forumController->get('/{campagne_id}/{topic_id}', function($campagne_id, $topic_id) use($app) {
	//$page = $app["postService"]->getLastPageOfPost($topic_id);
        $page = 1;
	return $app->redirect($app->path('topic_page', array('campagne_id' => $campagne_id, 'topic_id' => $topic_id, 'no_page' => $page)));
})->bind("topic");

$forumController->get('/{campagne_id}/{topic_id}/page/{no_page}', function($campagne_id, $topic_id, $no_page) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$posts = $app["postService"]->getPostsInTopic($topic_id, $no_page);
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
	$last_page = $app["postService"]->getLastPageOfPost($topic_id);

		foreach ($posts as &$post)
		{
			if($campagne_id > 0)
			{
				$post = preg_replace_callback('#\[(private|prv)(?:=(.*,?))?\](.*)\[/\1\]#isU',
				function ($matches) use ($is_mj,$app,$perso,$post){

					$txt = '';
					$txtDenied = '';
					if($matches[2] != '')
					{
						$txt = '<b><p size="small">Visible par : MJ, ' . $matches[2] . '</p></b>';
						$txtDenied = '<br>Une partie de ce message est en privée et ne vous est pas accessible.<br>';
					}
					$ret = '<div style="background-color: #EBEADD; padding:15px">' . $txtDenied . '</div>';
					if($is_mj || !isset($perso['name']) || strcasecmp($perso['name'],$post['perso_name']) == 0)
						$ret = '<div style="background-color: #EBEADD; padding:15px ">' . $txt . $matches[3] . '</div>';
					else
					{
						$users = preg_split("#,#", $matches[2]);
						foreach($users as $user)
						{
							if(strcasecmp($app['session']->get('user')['login'],$user) == 0 || strcasecmp($perso['name'],$user) == 0)
							{
								$ret = '<div style="background-color: #EBEADD; padding: 15px">' . $txt . $matches[3] . '</div>';
								break;
							}
						}
					}
					return $ret;
					},
					$post
				);
			}
			$post = replace_hide($post);
		}

	return $app->render('forum_topic.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'posts' => $posts,
			'perso' => $perso, 'is_mj' => $is_mj, 'personnages' => $personnages,
			"last_page" => $last_page, 'actual_page' => $no_page]);
})->bind("topic_page");


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
	$app["topicService"]->backwardLastPost($topic_id, $post_id);
	$post =  $app["postService"]->deletePost($post_id);
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->redirect($app->path('topic', array('campagne_id' => $campagne_id, 'topic_id' => $topic_id)));
})->bind("post_delete");

$forumController->post('/{campagne_id}/post/save', function(Request $request, $campagne_id) use($app) {
	$campagne_id = getInterneCampagneNumber($campagne_id);
	$topicId = $request->get('topic_id');
	$post_id = 0;
	if ($request->get('id') == '') {
		$post_id = $app["postService"]->createPost($request);
		$app["topicService"]->updateLastPost($topicId, $post_id);
	} else {
		$post_id = $request->get('id');
		$app["postService"]->updatePost($request);
	}
	$campagne_id = getExterneCampagneNumber($campagne_id);
	return $app->redirect($app->path('topic', array('campagne_id' => $campagne_id, 'topic_id' => $topicId))."#post".$post_id);
})->bind("post_save");

$forumController->get('/{campagne_id}/section/delete/{section_id}', function($campagne_id, $section_id) use($app) {
	$nbTopics = $app["sectionService"]->getNbTopicInSection($section_id);
	if($nbTopics > 0) {
		$error =  "Cette section contient encore des sujets. Impossible de la supprimer.";
		$campagne_id = getInterneCampagneNumber($campagne_id);
		$topics = $app["sectionService"]->getAllSectionInCampagne($campagne_id);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$campagne_id = getExterneCampagneNumber($campagne_id);
		return $app->render('forum_campagne.html.twig', ['campagne_id' => $campagne_id, 'topics' => $topics, 'is_mj' => $is_mj, 'error' => $error]);
	} else {
		$app["sectionService"]->deleteSection($section_id);
		return $app->redirect($app->path('forum_campagne', array('campagne_id' => $campagne_id)));
	}
})->bind("section_delete");

$forumController->get('/{campagne_id}/topic/delete/{topic_id}', function($campagne_id, $topic_id) use($app) {
	$app["topicService"]->deleteTopic($topic_id);
	return $app->redirect($app->path('forum_campagne', array('campagne_id' => $campagne_id)));
})->bind("topic_delete");


$app->mount('/forum', $forumController);

?>
