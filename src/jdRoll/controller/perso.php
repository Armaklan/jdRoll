<?php
/**
 * Character (player or not) operation
 *
 * @package perso
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\JsonResponse;

	/*
	    Controller de campagne (sécurisé)
	*/
	$persoController = $app['controllers_factory'];
	$persoController->before($mustBeLogged);

    function getWidgets($app, $campagne_id, $persoWidgets) {
        $config = $app["campagneConfigService"]->getCampagneConfig($campagne_id);
        $config['widgets'] = json_decode($config['widgets']);
        $widgets = $config['widgets'];
        if(is_array($widgets)){
            foreach($widgets as &$widget) {
                $found = false;
                foreach($persoWidgets as $persoWidget) {
                    if($persoWidget->id == $widget->id) {
                        if($widget->type == 'jauge') {
                            $widget->up = $persoWidget->up;
                            $widget->low = $persoWidget->low;
                        }
                        $widget->value = $persoWidget->value;
                        $found = true;
                    }
                }
                if(!$found) {
                    if($widget->type == 'jauge') {
                        $widget->up = 0;
                        $widget->low = 0;
                    }
										if($widget->type == 'text') {
											$widget->value = "";
										} else {
											$widget->value = 0;
										}
                }
            }
        }
        return $widgets;
    }

    function getWidgetsFromRequest($app, $campagne_id, $request) {
        $config = $app["campagneConfigService"]->getCampagneConfig($campagne_id);
        $widgets = json_decode($config['widgets']);
        if(is_array($widgets)){
            foreach($widgets as &$widget) {
                $found = false;
                $index = 0;
                foreach($request->get('widgetid') as $persoWidget) {
                    if($persoWidget == $widget->id) {
                        if($widget->type == 'jauge') {
                            $widget->up = $request->get('widgetup')[$index];
                            $widget->low = $request->get('widgetlow')[$index];
                        }
                        $widget->value = $request->get('widgetvalue')[$index];
                        $found = true;
                    }
                    $index = $index + 1;
                }
                if(!$found) {
                    if($widget->type == 'jauge') {
                        $widget->up = 0;
                        $widget->low = 0;
                    }
                    $widget->value = 0;
                }
            }
        }
        return $widgets;
    }

		$persoController->get('/edit/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$perso = $app['persoService']->getPersonnageById($perso_id);
    $perso['widgets'] = getWidgets($app, $campagne_id, $perso['widgets']);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
    $cats = $app['persoService']->getAllPnjCat($campagne_id);
		return $app->render('perso/edit.html.twig', ['campagne_id' => $campagne_id, 'perso' => $perso, 'error' => "", 'is_mj' => $is_mj, 'cats' => $cats]);
	})->bind("perso_edit");

	$persoController->get('/view/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$perso = $app['persoService']->getPersonnageById($perso_id);
		if($perso["user_id"] != $app["session"]->get("user")["id"]) {
			$is_mj = $app["campagneService"]->isMj($campagne_id);
      $perso['publicDescription'] = $app["postContentService"]->transformAllTag($perso['publicDescription'],$perso['name'],$is_mj,$campagne_id);
			return $app->render('perso/view.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
		} else {
			return $app->redirect($app->path('perso_view_all', ['campagne_id' => $campagne_id]));
		}
	})->bind("perso_view");

	$persoController->get('/ajax/{campagne_id}/{perso_id}', function(Request $request, $campagne_id, $perso_id) use($app) {
		$perso = $app['persoService']->getPersonnageById($perso_id);
    $isMj = $app["campagneService"]->isMj($campagne_id);
    $userId = $app['session']->get('user')['id'];
    $template = "";
    $perso['publicDescription'] = $app["postContentService"]->transformAllTag($perso['publicDescription'],$perso['name'],$isMj,$campagne_id);
    $param = ['campagne_id' => $campagne_id, 'perso' => $perso, 'is_mj' => $isMj];
		if( $isMj || ($perso["user_id"] == $userId) ) {
      $perso['privateDescription'] = $app["postContentService"]->transformAllTag($perso['privateDescription'],$perso['name'],$isMj,$campagne_id);
			$template = $app->render('perso/view_all_modal.html.twig', $param);
		} else {
      $template = $app->render('perso/view_modal.html.twig', $param);
		}
        return new JsonResponse(['name' => $perso['name'], 'content' => $template->getContent()]);
	})->bind("perso_view_ajax");


	$persoController->get('/view_all_mj/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
        $is_mj = $app["campagneService"]->isMj($campagne_id);
        if(! $is_mj) {
            return $app->redirect($app->path('perso_view', ['campagne_id' => $campagne_id, 'perso_id' => $perso_id]));
        }
		$perso = $app['persoService']->getPersonnageById($perso_id);
    $perso['publicDescription'] = $app["postContentService"]->transformAllTag($perso['publicDescription'],$perso['name'],$is_mj,$campagne_id);
    $perso['privateDescription'] = $app["postContentService"]->transformAllTag($perso['privateDescription'],$perso['name'],$is_mj,$campagne_id);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		return $app->render('perso/view_all.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_view_all_mj");

	$persoController->get('/view_all/{campagne_id}', function($campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$perso = $app['persoService']->getPersonnage(true,$campagne_id, $player_id);
    $perso[0]['publicDescription'] = $app["postContentService"]->transformAllTag($perso[0]['publicDescription'],$perso['name'],$is_mj,$campagne_id);
    $perso[0]['privateDescription'] = $app["postContentService"]->transformAllTag($perso[0]['privateDescription'],$perso['name'],$is_mj,$campagne_id);
		return $app->render('perso/view_all.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso[0], 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_view_all");

	$persoController->post('/ajax/{campagne_id}/widget/{perso_id}', function(Request $request, $campagne_id, $perso_id) use($app) {
		$userId = $app['session']->get('user')['id'];
		$widgets = $request->getContent();
		$app['monolog']->addInfo($widgets);
		$app['persoService']->updatePersonnageWidgets($campagne_id, $perso_id, $widgets);
		$perso = $app['persoService']->getPersonnageById($perso_id);
		if ($perso["user_id"] != null) {
			$app["notificationService"]->alertModifPerso(
				$userId,
				$perso,
				$campagne_id,
				$app->path('perso_view_all', ['campagne_id' => $campagne_id]),
				$app->path('perso_view_all_mj', ['campagne_id' => $campagne_id, 'perso_id' => $perso['id'] ])
			);
		}
		return new JsonResponse("OK");
	});

	$persoController->post('/save/{campagne_id}', function($campagne_id, Request $request) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);

		$perso_id = $request->get('perso_id');
		try {
			if ($perso_id != "") {
          $widgets = getWidgetsFromRequest($app, $campagne_id, $request);
	    		$app['persoService']->updatePersonnage($campagne_id, $perso_id, $request, $widgets);
	    		$perso = $app['persoService']->getPersonnageById($perso_id);
          $perso['widgets'] = getWidgets($app, $campagne_id, $perso['widgets']);
	    		if ($perso["user_id"] != null) {
						$app["notificationService"]->alertModifPerso(
							$app['session']->get('user')['id'],
							$perso,
		    			$campagne_id,
							$app->path('perso_view_all', ['campagne_id' => $campagne_id]),
							$app->path('perso_view_all_mj', ['campagne_id' => $campagne_id, 'perso_id' => $perso['id'] ])
						);
					}
                $cats = $app['persoService']->getAllPnjCat($campagne_id);
	    		return $app->render('perso/edit.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => "", 'is_mj' => $is_mj, 'cats' => $cats]);
			} else {
                $widgets = getWidgetsFromRequest($app, $campagne_id, $request);
				$app['persoService']->insertPNJ($campagne_id, $request, $widgets);
				return $app->redirect($app->path('perso_pnj', ['campagne_id' => $campagne_id]));
			}
		} catch(Exception $e) {
			$perso = $app['persoService']->getPersonnageById($perso_id);
            $perso['widgets'] = getWidgets($app, $campagne_id, $perso['widgets']);
            $cats = $app['persoService']->getAllPnjCat($campagne_id);
			return $app->render('perso/edit.html.twig', ['campagne_id' => $campagne_id,'perso' => $perso, 'error' => $e->getMessage(), 'is_mj' => $is_mj, 'cats' => $cats, 'config' => $config]);
		}
	})->bind("perso_save");



	$persoController->get('/pnj/{campagne_id}', function($campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
    $pjs = $app['persoService']->getPersonnagesInCampagne($campagne_id);
		$app['monolog']->addInfo('perso ' . count($pjs));
		$pjs = array_filter($pjs, function ($k) {
			return $k['cat_id'] == 0;
		});
		$pnjs = $app['persoService']->getPNJInCampagne($campagne_id, $is_mj);
		return $app->render('perso/list.html.twig', ['campagne_id' => $campagne_id,'pnjs' => $pnjs, 'pjs' => $pjs, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("perso_pnj");

	$persoController->get('/pnj/{campagne_id}/add', function($campagne_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$perso = $app['persoService']->getBlankPnj($campagne_id);
        $config = $app["campagneConfigService"]->getCampagneConfig($campagne_id);
        $perso['technical'] = $config['template'];
        $perso['widgets'] = getWidgets($app, $campagne_id,[]);
        $cats = $app['persoService']->getAllPnjCat($campagne_id);
		return $app->render('perso/edit.html.twig', [
            'campagne_id' => $campagne_id,
            'perso' => $perso,
            'error' => "",
            'is_mj' => $is_mj,
            'cats' => $cats]);
	})->bind("perso_pnj_add");

	$persoController->get('/remove/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$player_id = $app['session']->get('user')['id'];
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$error = "";
		if ($is_mj) {
			try {
				$app["persoService"]->deletePersonnage($perso_id);
			} catch (Exception $e) {
				$error = $e->getMessage();
			}
		} else {
			$error = "Vous n'êtes pas le MJ de cette partie";
		}
		$persos = $app['persoService']->getPNJInCampagne($campagne_id, $is_mj);
		$pjs = $app['persoService']->getPersonnagesInCampagne($campagne_id);
		$pjs = array_filter($pjs, function ($k) {
			return $k['cat_id'] == 0;
		});
		$pnjs = $app['persoService']->getPNJInCampagne($campagne_id, $is_mj);
		return $app->render('perso/list.html.twig', ['campagne_id' => $campagne_id,'pnjs' => $pnjs, 'pjs' => $pjs, 'error' => $error, 'is_mj' => $is_mj]);
	})->bind("perso_pnj_del");

	$persoController->get('/attach/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
		$perso = $app['persoService']->getPersonnageById($perso_id);
		$is_mj = $app["campagneService"]->isMj($campagne_id);
		$users = $app["campagneService"]->getParticipant($campagne_id);
		return $app->render('perso/transfert.html.twig', ['campagne_id' => $campagne_id, 'perso' => $perso, 'error' => "", 'is_mj' => $is_mj, 'users' => $users]);
	})->bind("attach");

    $persoController->get('/detach/{campagne_id}/{perso_id}', function($campagne_id, $perso_id) use($app) {
        $perso = $app['persoService']->getPersonnageById($perso_id);
        var_dump($perso);
        $app['persoService']->detachPersonnageById($campagne_id, $perso_id);
        $hasNoPerso = $app['persoService']->getPersonnage(false, $campagne_id, $perso["user_id"]);
        if($hasNoPerso == null) {
            $user = $app['userService']->getById($perso['user_id']);
            var_dump($user);
            $app['persoService']->createPersonnage($campagne_id, $user["id"], $user["username"]);
        }
        return $app->redirect($app->path('perso_pnj', ['campagne_id' => $campagne_id]));
    })->bind("dettach");

	$persoController->post('/save_attach/{campagne_id}', function($campagne_id, Request $request) use($app) {
        $campagne = $app['campagneService']->getCampagne($campagne_id);
        if($campagne['is_multi_character'] == '0') {
            $app['persoService']->detachPersonnage($campagne_id, $request->get('user_id'));
        }
		$app['persoService']->attachPersonnage($campagne_id, $request->get('user_id'), $request->get('perso_id'));
		return $app->redirect($app->path('perso_pnj', ['campagne_id' => $campagne_id]));
	})->bind("save_attach");

        $persoController->get('{campagne_id}/pnj_cat/view/{id}', function($campagne_id, $id) use($app) {
		$cat = $app['persoService']->getPnjCat($id);
                $is_mj = $app["campagneService"]->isMj($campagne_id);
		return $app->render('perso/edit_category.htm.twig', ['campagne_id' => $campagne_id, 'cat' => $cat, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("pnj_cat");

	$persoController->get('{campagne_id}/pnj_cat/add/', function($campagne_id) use($app) {
		$cat = $app['persoService']->getBlankPnjCat($campagne_id);
                $is_mj = $app["campagneService"]->isMj($campagne_id);
		return $app->render('perso/edit_category.htm.twig', ['campagne_id' => $campagne_id, 'cat' => $cat, 'error' => "", 'is_mj' => $is_mj]);
	})->bind("pnj_cat_add");

        $persoController->get('{campagne_id}/pnj_cat/remove/{id}', function($campagne_id, $id) use($app) {
                $app['persoService']->deletePnjCat($id);
                return $app->redirect($app->path('perso_pnj', array('campagne_id' => $campagne_id)));
	})->bind("pnj_cat_del");

    $persoController->post('{campagne_id}/pnj_cat/save', function($campagne_id, Request $request) use($app) {
        if($request->get('id') == 0) {
            $cat = $app['persoService']->insertPnjCat($request);
        } else {
            $cat = $app['persoService']->updatePnjCat($request);
        }
	    return $app->redirect($app->path('perso_pnj', array('campagne_id' => $campagne_id)));
	})->bind("pnj_cat_save");

    $persoController->get('/generate-thumbnails', function() use($app) {
        $app['persoService']->generateThumbnails();
        return new JsonResponse('OK', 200);
    })->bind("pnj_generate_thumbnails");

	$app->mount('/perso', $persoController);
