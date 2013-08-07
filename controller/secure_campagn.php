<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
  Controller de campagne (sécurisé)
 */
$securedCampagneController = $app['controllers_factory'];
$securedCampagneController->before($mustBeLogged);

$securedCampagneController->get('/new', function() use($app) {
            $campagne = $app['campagneService']->getBlankCampagne();
            return $app->render('campagne_form.html.twig', ['campagne' => $campagne, 'error' => ""]);
        })->bind("campagne_new");

$securedCampagneController->get('/{id}/edit', function($id) use($app) {
            $campagne = $app['campagneService']->getCampagne($id);
            return $app->render('campagne_form.html.twig', ['campagne' => $campagne, 'error' => ""]);
        })->bind("campagne_edit");

$securedCampagneController->get('/{campagne_id}/config/edit', function($campagne_id) use($app) {
            $campagne = $app['campagneService']->getCampagneConfig($campagne_id);
            return $app->render('campagne_config_form.html.twig', ['campagne_id' => $campagne['campagne_id'], 'campagne' => $campagne, 'is_mj' => true, 'error' => ""]);
        })->bind("campagne_config_edit");

$securedCampagneController->post('/config/save', function(Request $request) use($app) {
            try {
                $app['campagneService']->updateCampagneConfig($request);
                return $app->redirect($app->path('campagne_config_edit', array('campagne_id' => $request->get('campagne_id'))));
            } catch (Exception $e) {
                $campagne = $app['campagneService']->getFormCampagneConfig($request);
                return $app->render('campagne_config_form.html.twig', ['campagne_id' => $request->get('campagne_id'), 'campagne' => $campagne, 'is_mj' => true, 'error' => $e->getMessage()]);
            }
        })->bind("campagne_config_save");


$securedCampagneController->get('/join/{id}', function($id) use($app) {
            try {
                $campagne = $app['campagneService']->addJoueur($id, $app['session']->get('user')['id']);
                return $app->redirect($app->path('campagne_my_list'));
            } catch (Exception $e) {
                $campagnes = $app['campagneService']->getOpenCampagne();
                return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => $e->getMessage()]);
            }
        })->bind("campagne_join");

$securedCampagneController->get('/join/{id}/valid/{user_id}', function($id, $user_id) use($app) {
            try {
                $app['campagneService']->validJoueur($id, $user_id);
                $perso = $app['persoService']->getPersonnage(true, $id, $user_id);
                $config = $app['campagneService']->getCampagneConfig($id);
                $app['persoService']->setTechnical($perso['id'], $config['template']);

                $user = $app['userService']->getById($user_id);
                $campagne = $app['campagneService']->getCampagne($id);
                $campagne_name = $campagne['name'];
                $url_forum = $app->path('forum_campagne', array('campagne_id' => $id));
                $url_perso = $app->path('perso_edit', array('campagne_id' => $id));
                $content = "
                    <p>Votre inscription à '$campagne_name' a été validé par le MJ.</p>
                    <p>Nous t'invitons à te manifester sur le Mod-Off de la partie.
                    Le <a href='$url_forum'>forum de celle-ci est accessible ici.</a></p>
                    <p>Ta <a href='$url_perso'>fiche de personnage est accessible ici.</a></p>
                    <br>
                    <p>Nous te souhaitons une bonne partie.</p>
                    <p>Ludiquement.</p>
                ";
                $destinataires = array($user['username']);
                $app['messagerieService']->sendMessageWith($app['session']->get('user')['id'], $app['session']->get('user')['login'], "Notification système - inscription validé", $content, $destinataires);
                return $app->redirect($app->path('campagne', array('id' => $id)));
            } catch (Exception $e) {
                $campagnes = $app['campagneService']->getOpenCampagne();
                return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => $e->getMessage()]);
            }
        })->bind("campagne_join_valid");

$securedCampagneController->get('/quit/{id}', function($id) use($app) {
            try {
                $campagne = $app['campagneService']->removeJoueur($id, $app['session']->get('user')['id']);
                return $app->redirect($app->path('campagne_my_list'));
            } catch (Exception $e) {
                $campagnes = $app['campagneService']->getOpenCampagne();
                return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => $e->getMessage()]);
            }
        })->bind("campagne_quit");

$securedCampagneController->get('/open_subscribe/{id}', function($id) use($app) {
            $campagne = $app['campagneService']->openSubscribe($id);
            return $app->redirect($app->path('campagne_my_list'));
        })->bind("campagne_open_subscribe");

$securedCampagneController->get('/close_subscribe/{id}', function($id) use($app) {
            $campagne = $app['campagneService']->closeSubscribe($id);
            return $app->redirect($app->path('campagne_my_list'));
        })->bind("campagne_close_subscribe");

$securedCampagneController->post('/alarm', function(Request $request) use($app) {
            $campagne = $request->get("campagne");
            $joueur = $request->get("joueur");
            $statut = $request->get("statut");

            if($statut == 0) {
                $app["campagneService"]->removeAlert($campagne, $joueur);
            } else {
                $app["campagneService"]->addAlert($campagne, $joueur);
            }
            return "";
        })->bind("alarm");
$securedCampagneController->get('/ban/{id}/{user_id}', function($id, $user_id) use($app) {
            $user = $app['userService']->getById($user_id);
            $campagne = $app['campagneService']->getCampagne($id);
            $campagne_name = $campagne['name'];
            $app['campagneService']->removeJoueur($id, $user_id);
            $content = "Vous avez désinscrit de la partie de $campagne_name par le MJ (Refus d'inscription ou désinscription forcée).";
            $destinataires = array($user['username']);
            $app['messagerieService']->sendMessageWith($app['session']->get('user')['id'], $app['session']->get('user')['login'], "Notification système - inscription partie", $content, $destinataires);
            return $app->redirect($app->path('campagne', array('id' => $id)));
        })->bind("campagne_ban");

$securedCampagneController->get('/my_list', function() use($app) {
            $campagnesWithWaitingPj = $app['campagneService']->getMyCampagnesWithWaiting();
            $campagnes = $app['campagneService']->getMyCampagnes();
            $campagnesPj = $app['campagneService']->getMyPjCampagnes();
            $favorisedCampagne = $app['campagneService']->getFavorisedCampagne();
            $campagnesWaiting = $app['campagneService']->getMyWaitingPjCampagnes();
            $campagnesMjArchive = $app['campagneService']->getMyMjArchiveCampagnes();
            $campagnesPjArchive = $app['campagneService']->getMyPjArchiveCampagnes();
            return $app->render('campagne_my_list.html.twig', ['campagnes' => $campagnes, 'campagnes_pj' => $campagnesPj, 'favorised_campagne' => $favorisedCampagne,
                        'campagnes_mj_archive' => $campagnesMjArchive, 'campagnes_pj_archive' => $campagnesPjArchive,
                        'campagnes_waiting' => $campagnesWaiting, 'campagnes_with_waiting' => $campagnesWithWaitingPj, 'error' => ""]);
        })->bind("campagne_my_list");

$securedCampagneController->post('/save', function(Request $request) use($app) {
            try {
                if ($request->get('id') == '') {
                    $campagne_id = $app['campagneService']->createCampagne($request);
                    $app['campagneService']->createCampagneConfig($campagne_id);
                    $app['sectionService']->createSectionWith($campagne_id, "Partie", 1, 0, "");
                    $modoff = $app['sectionService']->createSectionWith($campagne_id, "Mod-Off", 2, 0, "");
                    $app['topicService']->createTopicWith($modoff, "Recrutement", 0, 0, 0, 2);
                    $app['sectionService']->createSectionWith($campagne_id, "Système et Contexte", 3, 0, "");
                    return $app->redirect($app->path('campagne_my_list'));
                } else {
                    $app['campagneService']->updateCampagne($request);
                    return $app->redirect($app->path('campagne_my_list'));
                }
            } catch (Exception $e) {
                $campagne = $app['campagneService']->getFormCampagne($request);
                return $app->render('campagne_form.html.twig', ['campagne' => $campagne, 'error' => $e->getMessage()]);
            }
        })->bind("campagne_save");


$securedCampagneController->get('/sidebar_large/{campagne_id}', function(Request $request, $campagne_id) use($app) {
            $player_id = $app['session']->get('user')['id'];
            $perso = $app['persoService']->getPersonnage(false, $campagne_id, $player_id);
            $allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id);
            $campagne = $app['campagneService']->getCampagne($campagne_id);
            $pjCampagnes = $app['campagneService']->getMyActivePjCampagnes();
            $mjCampagnes = $app['campagneService']->getMyActiveMjCampagnes();
            $favorisedCampagne = $app['campagneService']->getFavorisedCampagne();
            $config = $app['campagneService']->getCampagneConfig($campagne_id);
            $alert = $app['campagneService']->hasAlert($campagne_id, $player_id);
            return $app->render('sidebar_campagne_large.html.twig', ['campagne_id' => $campagne_id, 'perso' => $perso, 'favorised_campagne' => $favorisedCampagne,
                        'allPerso' => $allPerso, 'campagne' => $campagne, 'active_campagnes' => $mjCampagnes, 'active_pj_campagnes' => $pjCampagnes,
                        'config' => $config, 'alert' => $alert]);
        })->bind("sidebar_campagne_large");

$securedCampagneController->get('/sidebarmj_large/{campagne_id}', function(Request $request, $campagne_id) use($app) {
            $player_id = $app['session']->get('user')['id'];
            $allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id);
            $campagne = $app['campagneService']->getCampagne($campagne_id);
            $pjCampagnes = $app['campagneService']->getMyActivePjCampagnes();
            $mjCampagnes = $app['campagneService']->getMyActiveMjCampagnes();
            $favorisedCampagne = $app['campagneService']->getFavorisedCampagne();
            $config = $app['campagneService']->getCampagneConfig($campagne_id);
            $alert = $app['campagneService']->hasAlert($campagne_id, $player_id);
            return $app->render('sidebar_mj_campagne_large.html.twig', ['campagne_id' => $campagne_id, 'allPerso' => $allPerso,'favorised_campagne' => $favorisedCampagne,
                        'campagne' => $campagne, 'active_campagnes' => $mjCampagnes, 'active_pj_campagnes' => $pjCampagnes,
                        'config' => $config, 'alert' => $alert]);
        })->bind("sidebar_campagne_mj_large");

$securedCampagneController->get('/dicer/view/{campagne_id}', function($campagne_id) use($app) {
            $jets = $app['dicerService']->getDice($campagne_id);
            return $app->render('dicer.html.twig', ['campagne_id' => $campagne_id, 'jets' => $jets]);
        })->bind("print_dicer");

$securedCampagneController->post('/dicer/{campagne_id}/{topic_id}', function(Request $request, $campagne_id, $topic_id) use($app) {
            try {
                $player_id = $app['session']->get('user')['id'];
                $param = $request->get('param');
                $description = $request->get('description');
                $result = $app['dicerService']->launchDice($campagne_id, $param, $description);
                if ($topic_id != 0) {
                    $player = $app['userService']->getCurrentUser();
                    $name = $player['username'];
                    $post_id = $app['postService']->createDicerPost($topic_id, " $name a lancé $param et a obtenu : $result . <br> Description : $description ");
                }
                return $result;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        })->bind("dicer");

$securedCampagneController->get('/notes/view/{campagne_id}', function($campagne_id) use($app) {
            $user_id = $app['session']->get('user')['id'];
            $content = $app['campagneService']->getNote($campagne_id, $user_id);
            return $app->render('notes.html.twig', ['campagne_id' => $campagne_id, 'content_notes' => $content]);
        })->bind("notes_popup");

$securedCampagneController->post('/notes/update/{campagne_id}', function($campagne_id, Request $request) use($app) {
            $content = $request->get('content');
            $user_id = $app['session']->get('user')['id'];
            $app['campagneService']->updateNote($campagne_id, $user_id, $content);
            return "";
        })->bind("notes_update");

$app->mount('/campagne', $securedCampagneController);
?>
