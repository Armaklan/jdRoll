<?php
  /**
 * Control top heading forum Operation
 *
 * @package forum
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
  $annonces = $app['annonceService']->get();
  $is_mj = $app["campagneService"]->isMj(null);
  $isAdmin = $app["campagneService"]->isAdmin();
  $campagne = $app["campagneService"]->getBlankCampagne();
  $users = $app['userService']->getAllUsers();
  return $app->render('game/home.html.twig', ['absences' => array(), 'campagne_id' => 0,
                                              'annonces' => $annonces,
                                              'topics' => $topics, 'is_mj' => $is_mj, 'error' => '',
                                              'users' => $users,
                                              'is_participant' => false,
                                              'isAdmin' => $isAdmin, 'campagne' => $campagne]);
})->bind("forum");

$forumController->get('/{campagne_id}', function($campagne_id) use($app) {
  $user_id = $app['session']->get('user')['id'];
  $users = [];
  $annonces = [];
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $campagne = $app["campagneService"]->getBlankCampagne();
  if($campagne_id != null) {
    $campagne = $app["campagneService"]->getCampagne($campagne_id);
  } else {
    $users = $app['userService']->getAllUsers();
    $annonces = $app['annonceService']->get();
  }
  $topics = $app["sectionService"]->getAllSectionInCampagne($campagne_id);
  $is_mj = $app["campagneService"]->isMj($campagne_id);
  $isAdmin = $app["campagneService"]->isAdmin();
  $isParticipant = $app["campagneService"]->isParticipant($campagne_id);
  $campagne_id = getExterneCampagneNumber($campagne_id);
  $absences = $app["absenceService"]->getFutureAbsenceInCampagn($campagne_id);
  $waitingUsers = $app["campagneService"]->getParticipantByStatus($campagne_id,0);
  $isFavoris = $app["favorisService"]->isFavoris($campagne_id, $user_id);
  return $app->render('game/home.html.twig', ['absences' => $absences, 'is_favoris' => $isFavoris,
                                              'campagne_id' => $campagne_id, 'topics' => $topics,
                                              'annonces' => $annonces,
                                              'is_participant' => $isParticipant,
                                              'users' => $users,
                                              'isAdmin' => $isAdmin, 'is_mj' => $is_mj, 'error' => '','waitingUsers' => $waitingUsers, 'campagne' => $campagne]);
})->bind("forum_campagne");

$forumController->get('/{campagne_id}/section/add', function($campagne_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $section =  $app["sectionService"]->getBlankSection($campagne_id);
  $is_mj = $app["campagneService"]->isMj($campagne_id);
  $campagne_id = getExterneCampagneNumber($campagne_id);
  return $app->render('game/forum/section_edit.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => '', 'is_mj' => $is_mj]);
})->bind("section_add");

$forumController->get('/{campagne_id}/section/edit/{section_id}', function($campagne_id, $section_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $section =  $app["sectionService"]->getSection($section_id);
  $is_mj = $app["campagneService"]->isMj($campagne_id);
  $campagne_id = getExterneCampagneNumber($campagne_id);
  return $app->render('game/forum/section_edit.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => '', 'is_mj' => $is_mj]);
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
    return $app->render('game/forum/section_edit.html.twig', ['campagne_id' => $campagne_id, 'section' => $section, 'error' => $e->getMessage(), 'is_mj' => $is_mj]);
  }
})->bind("section_save");

$forumController->get('/{campagne_id}/topic/add/{section_id}', function($campagne_id, $section_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $topic =  $app["topicService"]->getBlankTopic($section_id);
  $sections =  $app["sectionService"]->getSections($campagne_id);
  $is_mj = $app["campagneService"]->isMj($campagne_id);
  $allPerso = $app['persoService']->getPersonnagesInCampagneLinkTopic($campagne_id, 0);
  $campagne_id = getExterneCampagneNumber($campagne_id);
  return $app->render('game/forum/topic_edit.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'error' => '',
                                                          'sections' => $sections, 'is_mj' => $is_mj, 'persos' => $allPerso]);
})->bind("topic_add");

$forumController->get('/{campagne_id}/topic/edit/{topic_id}', function($campagne_id, $topic_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $topic =  $app["topicService"]->getTopic($topic_id);
  $is_mj = $app["campagneService"]->isMj($campagne_id);
  $sections =  $app["sectionService"]->getSections($campagne_id);
  $allPerso = $app['persoService']->getPersonnagesInCampagneLinkTopic($campagne_id, $topic_id);
  $campagne_id = getExterneCampagneNumber($campagne_id);
  return $app->render('game/forum/topic_edit.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'error' => '',
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
    return $app->render('game/forum/topic_edit.html.twig', ['campagne_id' => $campagne_id,'topic' => $topic, 'error' => $e->getMessage(), 'is_mj' => $is_mj, 'persos' => $allPerso]);
  }
})->bind("topic_save");

$forumController->post('/{campagne_id}/topic/draft', function($campagne_id, Request $request) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  try {
    $topicId = $app["postService"]->createDraft($request);
    $campagne_id = getExterneCampagneNumber($campagne_id);
    return "Enregistrement effectué";
  } catch(Exception $e) {
    return "Erreur lors de l'enregistrement";
  }
})->bind("draft_save");


$forumController->get('/{campagne_id}/readall', function($campagne_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $topics = $app["sectionService"]->getAllSectionInCampagne($campagne_id);
  foreach($topics as &$topic) {
    if($topic['posts_id'] > 0) {
      $app["postService"]->markRead($topic['posts_id'], $topic['topics_id']);
    }
  }
  $campagne_id = getExterneCampagneNumber($campagne_id);
  return $app->redirect($app->path('forum_campagne', array('campagne_id' => $campagne_id)));
})->bind("campagne_read_all");

$forumController->get('/{campagne_id}/{topic_id}', function($campagne_id, $topic_id) use($app) {
  //$page = $app["postService"]->getLastPageOfPost($topic_id);
  $page = 1;
  return $app->redirect($app->path('topic_page', array('campagne_id' => $campagne_id, 'topic_id' => $topic_id, 'no_page' => $page)));
})->bind("topic");


$forumController->get('/{campagne_id}/{topic_id}/all', function($campagne_id, $topic_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $posts = $app["postService"]->getAllPostsInTopic($topic_id);
  $topic = $app["topicService"]->getTopic($topic_id);
  $perso = $app['persoService']->getPersonnage(false, $campagne_id, $app['session']->get('user')['id']);
  $is_mj = $app["campagneService"]->isMj($campagne_id);
  $personnages = $app['persoService']->getAllPersonnagesInCampagne($campagne_id);
  $config = $app["campagneConfigService"]->getCampagneConfig($campagne_id);
  $default_perso = '';
  if($config != null) {
    $default_perso = $config['default_perso_id'];
  }
  $last_page = $app["postService"]->getLastPageOfPost($topic_id);
  if($campagne_id > 0) {
    foreach ($posts as &$post){
      $post['post_content'] = $app["postContentService"]->transformAllTag($post['post_content'], $post['perso_name'],$is_mj,$campagne_id);
    }
  }
  $campagne_id = getExterneCampagneNumber($campagne_id);
  $draft = $app["postService"]->getDraft($topic_id, $app['session']->get('user')['id']);
  return $app->render('game/forum/topic_post_list.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'posts' => $posts,
                                                               'perso' => $perso, 'is_mj' => $is_mj, 'personnages' => $personnages, 'draft' => $draft, 'default_perso' => $default_perso,
                                                               "last_page" => $last_page, 'actual_page' => 0, 'config' => $config]);
})->bind("topic_all");

$forumController->post('/{campagne_id}/preview', function(Request $request, $campagne_id) use($app) {
  $post = $request->get('content');
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $perso = $app['persoService']->getPersonnage(false, $campagne_id, $app['session']->get('user')['id']);
  $is_mj = $app["campagneService"]->isMj($campagne_id);
  $content = $app["postContentService"]->transformAllTag($post, "",$is_mj,$campagne_id);
  return new JsonResponse(array('content' => $content), 200);
});

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
  $config = $app["campagneConfigService"]->getCampagneConfig($campagne_id);
  $default_perso = '';
  if($config != null) {
    $default_perso = $config['default_perso_id'];
  }
  $last_page = $app["postService"]->getLastPageOfPost($topic_id);

  if($campagne_id > 0) {
    foreach ($posts as &$post){
      $post['post_content'] = $app["postContentService"]->transformAllTag($post['post_content'], $post['perso_name'],$is_mj,$campagne_id);
    }
  }
  $campagne_id = getExterneCampagneNumber($campagne_id);
  $draft = $app["postService"]->getDraft($topic_id, $app['session']->get('user')['id']);
  return $app->render('game/forum/topic_post_list.html.twig', ['campagne_id' => $campagne_id, 'topic' => $topic, 'posts' => $posts,
                                                               'perso' => $perso, 'is_mj' => $is_mj, 'personnages' => $personnages, 'draft' => $draft, 'default_perso' => $default_perso,
                                                               "last_page" => $last_page, 'actual_page' => $no_page, 'config' => $config]);
})->bind("topic_page");


$forumController->get('/{campagne_id}/post/edit/{post_id}', function($campagne_id, $post_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $post =  $app["postService"]->getPost($post_id);
  $personnages = $app['persoService']->getAllPersonnagesInCampagne($campagne_id);
  $is_mj = $app["campagneService"]->isMj($campagne_id);
  $campagne_id = getExterneCampagneNumber($campagne_id);
  $playerPersonnages = $app['persoService']->getPersonnage(false, $campagne_id, $app['session']->get('user')['id']);
  return $app->render('game/forum/post_edit.html.twig', ['campagne_id' => $campagne_id, 'post' => $post,
                                                         'error' => '', 'is_mj' => $is_mj, 'personnages' => $personnages, 'player_personnages' => $playerPersonnages]);
})->bind("post_edit");

$forumController->get('/{campagne_id}/post/deletePost/{topic_id}/{post_id}', function($campagne_id, $topic_id, $post_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $app["topicService"]->backwardLastPost($topic_id, $post_id);
  $post =  $app["postService"]->deletePost($post_id);
  return new JsonResponse("Suppression effectué avec succès", 200);
})->bind("post_delete");

$forumController->post('/{campagne_id}/post/save', function(Request $request, $campagne_id) use($app) {
  $campagne_id = getInterneCampagneNumber($campagne_id);
  $topicId = $request->get('topic_id');
  $post_id = 0;
  $isNew = false;
  if ($request->get('id') == '') {
    $post_id = $app["postService"]->createPost($request);
    try {
      $app["postService"]->deleteDraft($request);
    } catch (Exception $e) {
      //tant pis si on supprime pas le draft
    }
    $isNew = true;
    $app["topicService"]->updateLastPost($topicId, $post_id);
  } else {
    $post_id = $request->get('id');
    $app["postService"]->updatePost($request);
  }
  $campagne_id = getExterneCampagneNumber($campagne_id);
  $url = $app->path('topic', array('campagne_id' => $campagne_id, 'topic_id' => $topicId))."#post".$post_id;
  if($isNew) {
    $app["notificationService"]->alertPostInCampagne(
      $app["session"]->get('user')['id'],
      $campagne_id,
      $topicId,
      $url
    );
  }
  return new JsonResponse($url);
})->bind("post_save");

$forumController->get('/{campagne_id}/section/delete/{section_id}', function($campagne_id, $section_id) use($app) {
  $nbTopics = $app["sectionService"]->getNbTopicInSection($section_id);
  if($nbTopics > 0) {
    $error =  "Cette section contient encore des sujets. Impossible de la supprimer.";
    $campagne_id = getInterneCampagneNumber($campagne_id);
    $topics = $app["sectionService"]->getAllSectionInCampagne($campagne_id);
    $is_mj = $app["campagneService"]->isMj($campagne_id);
    $campagne = $app["campagneService"]->getBlankCampagne();
    if($campagne_id != null) {
      $campagne = $app["campagneService"]->getCampagne($campagne_id);
    }
    $campagne_id = getExterneCampagneNumber($campagne_id);
    return $app->render('game/home.html.twig', ['campagne_id' => $campagne_id, 'topics' => $topics, 'is_mj' => $is_mj, 'error' => $error, 'campagne' => $campagne]);
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
