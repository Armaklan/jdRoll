<?php
/**
 * Control feedback module
 *
 * @package chat
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */


	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\JsonResponse;

	$feedbackController = $app['controllers_factory'];

	$feedbackController->post('/', function(Request $request) use($app) {
        $user = $app['session']->get('user');
        $title = $request->get('title');
        $content = $request->get('content');
        if(($title == "") || ($content == "")) {
            return new JsonResponse('Merci de saisir un titre et un contenu', 400);
        } else {
		    $feedback = $app['feedbackService']->create($user, $title, $content);
            return new JsonResponse($feedback, 201);
        }
	})->bind("feedback_post")->before($mustBeLogged);

    $feedbackController->get('/{id}', function($id) use($app) {

	})->bind("feedback_get")->before($mustBeLogged);

    $feedbackController->delete('/{id}', function($id) use($app) {
        $feedback = $app['feedbackService']->delete($id);
        return new JsonResponse($feedback, 200);
    })->bind("feedback_get")->before($mustBeLogged);

    $feedbackController->get('/', function() use($app) {
		$feedbacks = $app['feedbackService']->getOpenFeedbacks();
        $isAdmin = $app["campagneService"]->IsAdmin();
        return $app->render('feedbacks.html.twig', ['feedbacks' => $feedbacks, 'is_admin' => $isAdmin, 'error' => ""]);
	})->bind("feedback_list")->before($mustBeLogged);

	$app->mount('/feedback', $feedbackController);

?>
