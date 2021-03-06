<?php
/**
 * Control secured campagne operation
 *
 * @package secure_campagn
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/*
  Controller de campagne (sécurisé)
 */
$securedCampagneController = $app['controllers_factory'];
$securedCampagneController->before($mustBeLogged);

$securedCampagneController->get('/new', function() use($app) {
            $campagne = $app['campagneService']->getBlankCampagne();
            return $app->render('game/config/add.html.twig', ['campagne' => $campagne, 'error' => ""]);
        })->bind("campagne_new");

$securedCampagneController->get('/{id}/edit', function($id) use($app) {
            $campagne = $app['campagneService']->getCampagne($id);
            return $app->render('game/config/add.html.twig', ['campagne' => $campagne, 'error' => ""]);
        })->bind("campagne_edit");

$securedCampagneController->get('/{campagne_id}/config/edit', function($campagne_id) use($app) {
            $campagne = $app['campagneService']->getCampagne($campagne_id);
            $config = $app["campagneConfigService"]->getCampagneConfig($campagne_id);
            $themes = $app['themeService']->all();
            $personnages = $app['persoService']->getAllPersonnagesInCampagne($campagne_id);
            $cartes = $app['carteService']->getAllCartes($campagne_id, true);
            $is_mj = $app["campagneService"]->isMj($campagne_id);
            $participants = $app["campagneService"]->getParticipant($campagne_id);
            return $app->render('game/config/edit.html.twig', [
                'campagne_id' => $campagne_id,
                'config' => $config,
                'is_mj' => $is_mj,
                'participants' => $participants,
                'campagne' => $campagne,
                'themes' => $themes,
                'personnages' => $personnages,
                'cartes' => $cartes,
                'is_mj' => true,
                'error' => ""
            ]);
        })->bind("campagne_config_edit");

$securedCampagneController->post('/config/save', function(Request $request) use($app) {
            try {
                $app['campagneConfigService']->updateCampagneConfig($request);
                return $app->redirect($app->path('campagne_config_edit', array('campagne_id' => $request->get('campagne_id'))));
            } catch (Exception $e) {
                $campagne = $app['campagneConfigService']->getFormCampagneConfig($request);
                return $app->render('game/config/edit.html.twig', ['campagne_id' => $request->get('campagne_id'), 'campagne' => $campagne, 'is_mj' => true, 'error' => $e->getMessage()]);
            }
        })->bind("campagne_config_save");

$securedCampagneController->post('/{campagne}/perso_widgets', function(Request $request, $campagne) use($app) {
            try {
                $app['campagneConfigService']->updateWidgetsConfig($campagne, $request->getContent());
                return new JsonResponse("");
            } catch (Exception $e) {
                return new JsonResponse($e->getMessage(),500);
            }
        });

$securedCampagneController->get('/join/{id}', function($id) use($app) {
            try {
                $campagne = $app['campagneService']->addJoueur($id, $app['session']->get('user')['id']);
				$url = $app->path("campagne", ['id' => $campagne['id']]);
				$app['notificationService']->alertJoinCampagne($campagne, $app['session']->get('user')['login'],$url);
                return $app->redirect($app->path('campagne_my_list'));
            } catch (Exception $e) {
                $campagnes = $app['campagneService']->getOpenCampagne();
                return $app->render('campagne_list.html.twig', ['campagnes' => $campagnes, 'error' => $e->getMessage()]);
            }
        })->bind("campagne_join");


$securedCampagneController->get('/quit/{id}', function($id) use($app) {
            try {
				$campagne = $app['campagneService']->getCampagne($id);
                $app['campagneService']->removeJoueur($id, $app['session']->get('user')['id']);
				$url = $app->path("campagne", ['id' => $campagne['id']]);
				$app['notificationService']->alertQuitCampagne($campagne, $app['session']->get('user')['login'],$url);
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


$securedCampagneController->get('/valid/{id}/{user_id}', function($id, $user_id) use($app) {
            try {
                $app['campagneService']->validJoueur($id, $user_id);
                $user = $app['userService']->getById($user_id);
                $perso = $app['persoService']->createPersonnage($id, $user_id, $user['username']);
                $config = $app["campagneConfigService"]->getCampagneConfig($id);
                $app['persoService']->setTechnical($perso['id'], $config['template']);


                $campagne = $app['campagneService']->getCampagne($id);
                $campagne_name = $campagne['name'];
                $url_forum = $app->path('forum_campagne', array('campagne_id' => $id));
                $url_perso = $app->path('perso_edit', array('campagne_id' => $id, 'perso_id' => $perso));
                $content = "
                    <p>Votre inscription à '$campagne_name' a été validée par le MJ.</p>
                    <p>Nous t'invitons à te manifester sur le Mod-Off de la partie.
                    Le <a href='$url_forum'>forum de celle-ci est accessible ici.</a></p>
                    <p>Ta <a href='$url_perso'>fiche de personnage est accessible ici.</a></p>
                    <br>
                    <p>Nous te souhaitons une bonne partie.</p>
                    <p>Ludiquement.</p>
                ";
                $destinataires = array($user['username']);
                $app['messagerieService']->sendMessageWith($app['session']->get('user')['id'], $app['session']->get('user')['login'], "Notification système - inscription validée", $content, $destinataires);
				$app['notificationService']->alertUserForMp("Système", $destinataires, "Inscription à une partie", $app->path('messagerie'));
                return new JsonResponse("Joueur accepté");
            } catch (Exception $e) {
                return new JsonResponse($e->getMessage(), 500);
            }
        })->bind("campagne_join_valid");

$securedCampagneController->get('/ban/{id}/{user_id}', function($id, $user_id) use($app) {
            $user = $app['userService']->getById($user_id);
            $campagne = $app['campagneService']->getCampagne($id);
            $campagne_name = $campagne['name'];
            $app['campagneService']->removeJoueur($id, $user_id);
            $content = "Vous avez désinscrit de la partie de $campagne_name par le MJ (Refus d'inscription ou désinscription forcée).";
            $destinataires = array($user['username']);
            $app['messagerieService']->sendMessageWith($app['session']->get('user')['id'], $app['session']->get('user')['login'], "Notification système - inscription partie", $content, $destinataires);
            return new JsonResponse("Joueur désinscrit");
        })->bind("campagne_ban");

$securedCampagneController->get('/my_list', function() use($app) {
            return $app->render('campagne_my_list.html.twig', ['error' => ""]);
        })->bind("campagne_my_list");


$securedCampagneController->post('/save', function(Request $request) use($app) {
            try {
                if ($request->get('id') == '') {
                    $campagne_id = $app['campagneService']->createCampagne($request);
                    $app['campagneConfigService']->createCampagneConfig($campagne_id);
                    $app['sectionService']->createSectionWith($campagne_id, "Partie", 1, 0, "");
                    $modoff = $app['sectionService']->createSectionWith($campagne_id, "Mod-Off", 2, 0, "");
                    $app['topicService']->createTopicWith($modoff, "Recrutement", 0, 0, 0, 2);
                    $app['sectionService']->createSectionWith($campagne_id, "Système et Contexte", 3, 0, "");
                    return $app->redirect($app->path('campagne_config_edit', ['campagne_id' => $campagne_id]));
                } else {
                    // FIX - Obsolète
                    $app['campagneService']->updateCampagne($request);
                    return $app->redirect($app->path('campagne_my_list'));
                }
            } catch (Exception $e) {
                $campagne = $app['campagneService']->getFormCampagne($request);
                return $app->render('game/config/add.html.twig', ['campagne' => $campagne, 'error' => $e->getMessage()]);
            }
        })->bind("campagne_save");

$securedCampagneController->post('/{id}/desc', function(Request $request, $id) use($app) {
            try {
                if ($id > 0) {
                    $app['campagneService']->updateCampagne($request);
                    return new JsonResponse('Mise à jour efectuée');
                } else {
                    return new JsonResponse('Campagne invalide', 400);
                }
            } catch (Exception $e) {
                return new JsonResponse($e->getMessage(), 500);
            }
        })->bind("campagne_save_ajax");

$securedCampagneController->post('/{id}/sheet', function(Request $request) use($app) {
            try {
                $app['campagneConfigService']->updateCampagneConfigSheet($request);
                return new JsonResponse('Mise à jour effectuée', 200);
            } catch (Exception $e) {
                return new JsonResponse('Problème durant la mise à jour', 500);
            }
        })->bind("campagne_config_sheet");

$securedCampagneController->post('/{id}/theme', function(Request $request) use($app) {
            try {
                $app['campagneConfigService']->updateCampagneConfigTheme($request);
                return new JsonResponse('Mise à jour effectuée', 200);
            } catch (Exception $e) {
                return new JsonResponse($e->getMessage(), 500);
            }
        })->bind("campagne_config_theme");

$securedCampagneController->post('/{id}/divers', function(Request $request) use($app) {
            try {
                $app['campagneConfigService']->updateCampagneConfigDivers($request);
                return new JsonResponse('Mise à jour effectuée', 200);
            } catch (Exception $e) {
                return new JsonResponse($e->getMessage(), 500);
            }
        })->bind("campagne_config_divers");

$securedCampagneController->get('/list_perso_js/{campagne_id}', function(Request $request, $campagne_id) use($app) {
    $allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id);
    return $app->render('list_perso_js.html.twig', ['campagne_id' => $campagne_id, 'allPerso' => $allPerso]);
})->bind("list_perso_js");

$securedCampagneController->get('/sidebar_large/{campagne_id}', function(Request $request, $campagne_id) use($app) {
            $player_id = $app['session']->get('user')['id'];
            $perso = $app['persoService']->getPersonnage(false, $campagne_id, $player_id);
            $allPerso = $app['persoService']->getPersonnagesInCampagne($campagne_id);
            $campagne = $app['campagneService']->getCampagne($campagne_id);
            $pjCampagnes = $app['campagneService']->getMyActivePjCampagnes();
            $mjCampagnes = $app['campagneService']->getMyActiveMjCampagnes();
            $prepaCampagnes = $app['campagneService']->getMyActivePrepaCampagnes();
            $favorisedCampagne = $app['campagneService']->getFavorisedCampagne();
            $config = $app["campagneConfigService"]->getCampagneConfig($campagne_id);
            $alert = $app['campagneService']->hasAlert($campagne_id, $player_id);
            $topics = $app["sectionService"]->getQuickAllSectionInCampagne($campagne_id);
            $cartes = $app["carteService"]->getAllCartes($campagne_id);
            $isMj = $app["campagneService"]->isMj($campagne_id);
            $isFavoris = $app["favorisService"]->isFavoris($campagne_id, $player_id);
            return $app->render('sidebar_campagne_large.html.twig', [
                'campagne_id' => $campagne_id,
                'perso' => $perso,
                'favorised_campagne' => $favorisedCampagne,
                'prepa_campagnes' => $prepaCampagnes,
                'topics' => $topics,
                'is_favoris' => $isFavoris,
                'is_mj' => $isMj,
                        'allCarte' => $cartes,
                        'allPerso' => $allPerso, 'campagne' => $campagne, 'active_campagnes' => $mjCampagnes, 'active_pj_campagnes' => $pjCampagnes,
                        'config' => $config, 'alert' => $alert]);
        })->bind("sidebar_campagne_large");

$securedCampagneController->get('/dicer/view/{campagne_id}', function($campagne_id) use($app) {
            $config = $app["campagneConfigService"]->getCampagneConfig($campagne_id);
            if($app["campagneService"]->isMj($campagne_id)) {
                $jets = $app['dicerService']->getAllDice($campagne_id);
            } else {
                $jets = $app['dicerService']->getUserDice($campagne_id, $app['session']->get('user')['id']);
            }
            return $app->render('dicer.html.twig', ['campagne_id' => $campagne_id, 'jets' => $jets, 'config' => $config]);
        })->bind("print_dicer");

$securedCampagneController->post('/dicer/{campagne_id}/{topic_id}', function(Request $request, $campagne_id, $topic_id) use($app) {
            try {
                $player_id = $app['session']->get('user')['id'];
                $param = $request->get('param');
                $description = $request->get('description');
                $result = $app['dicerService']->launchDice($campagne_id, $param, $description);
                $url = "";
                if ($topic_id != 0) {
                  $player = $app['userService']->getCurrentUser();
                  $name = $player['username'];
                  $post_id = $app['postService']->createDicerPost($topic_id, " $name a lancé $param et a obtenu : $result . <br> Description : $description ");
                  $url = $app->path('topic', array('campagne_id' => $campagne_id, 'topic_id' => $topic_id))."#post".$post_id;
                } else {
                  $url = $app->path('forum_campagne', array('campagne_id' => $campagne_id));
                }
                $app["notificationService"]->alertDiceInCampagne(
                  $app["session"]->get('user')['id'],
                  $campagne_id,
                  $topic_id,
                  $url
                );
                return $result;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        })->bind("dicer");

$securedCampagneController->post('/admin_open', function(Request $request) use($app) {
            $id = $request->get('id');
            $state = $request->get('state');
            $app['campagneService']->updateIsAdminOpen($id, $state);
            return "ok";
});


$app->mount('/campagne', $securedCampagneController);
?>
