<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/*
    Page globale (Index, Authentification, ...)
*/
$commonController = $app['controllers_factory'];

$commonController->get('/', function() use ($app) {
    $campagnes = $app['campagneService']->getLastCampagne();
    $open_campagne = $app["campagneService"]->getOpenCampagne();
    $last_users = $app['userService']->getLastSubscribe();
    $connected_24H_users = $app['userService']->getConnectedIn24H();
    $last_posts = $app['sectionService']->getLastPostInForum();
    $absences = $app['absenceService']->getCurrentAbsence();
	$birthDay = $app['userService']->getCurrentBirthDay();
	$isAdmin = $app["campagneService"]->IsAdmin();
    return $app->render('home.html.twig', ['open_campagne' => $open_campagne, 'campagnes' => $campagnes, 'last_users' => $last_users,
    		'connected_24H_users' => $connected_24H_users, 'last_posts' => $last_posts, 'absences' => $absences,'birthDays' => $birthDay,'isAdmin' => $isAdmin]);
})->bind("homepage");

$commonController->get('/login/{url}', function($url) use($app) {
    return $app->render('login.html.twig', ['error' => "", 'url' => $url]);
})->bind("login_page");

$commonController->post('/login', function(Request $request) use($app) {

	$login = $request->get('login');
	$password = $request->get('pass');
	$url = $request->get('url');

    try {
        $app["userService"]->login($login, $password);
    } catch (Exception $e) {
        return $app->render('login.html.twig', ['error' => $e->getMessage(), 'url' => $url]);
    }
	$finalUrl = str_replace('!', '/', $url);
	if ($finalUrl == "/") {
		$finalUrl = $app->path('homepage');
	}
    return $app->redirect($finalUrl);

})->bind("login_connect");

$commonController->get('/profile/{username}', function($username) use($app) {
    $user = $app["userService"]->getByUsername($username);
	$currentUser = $app["userService"]->getCurrentUser();
	$isAdmin = $app["campagneService"]->IsAdmin();
    $pjCampagnes = $app['campagneService']->getActivePjCampagnes($user['id']);
    $mjCampagnes = $app['campagneService']->getActiveMjCampagnes($user['id']);
    return $app->render('profile.html.twig', ['error' => "", 'user' => $user, 'pj_campagnes' => $pjCampagnes, 'mj_campagnes' => $mjCampagnes, 'currentUser' => $currentUser,'isAdmin' => $isAdmin]);
})->bind("profile");

$commonController->get('/profile/{username}/edit', function($username) use($app) {
    $user = $app["userService"]->getByUsername($username);
	$currentUser = $app["userService"]->getCurrentUser();

	 if($currentUser["username"] == $user["username"] || $currentUser["profil"] <= 0)
		 return $app->redirect($app->path('my_profile'));


    return $app->render('my_profile.html.twig', ['error' => "", 'user' => $user, 'adminUser' => $currentUser]);
})->bind("profile_edit");

$commonController->post('/profile/{username}/edit/save', function(Request $request) use($app) {
	$user = $app["userService"]->updateUser($request);
	$currentUser = $app["userService"]->getCurrentUser();
    return $app->render('my_profile.html.twig', ['error' => "", 'user' => $user, 'adminUser' => $currentUser]);
})->bind("profile_edit_save");

$commonController->get('/about', function() use($app) {
	return $app->render('about.html.twig');
})->bind("about");

$commonController->get('/chat', function() use($app) {
	return $app->render('tchat.html.twig');
})->bind("chat");


$commonController->get('/logout', function(Request $request) use($app) {
    $app["userService"]->logout();
    return $app->redirect($app->path('homepage'));
})->bind("logout");

$commonController->get('/sidebar/std_large', function() use ($app) {
	$pjCampagnes = $app['campagneService']->getMyActivePjCampagnes();
	$mjCampagnes = $app['campagneService']->getMyActiveMjCampagnes();
	$favorisedCampagne = $app['campagneService']->getFavorisedCampagne();
	return $app->render('sidebar_std_large.html.twig', ['active_campagnes' => $mjCampagnes, 'active_pj_campagnes' => $pjCampagnes, 'favorised_campagne' => $favorisedCampagne]);
})->bind("sidebar_std_large");

$commonController->post('/upload', function(Request $request) use ($app) {
	$file = $request->files->get("uploadFile");
	$filename = $request->get('filename');
	if($filename == null || $filename == "") {
		$filename = $file->getClientOriginalName();
		$ext = explode("/",$file->getClientMimeType())[1];
		$filename = sha1($filename . microtime()) . "." . $ext;
		$file->move(__DIR__.'/../files', $filename);
	}
	return $app->path('homepage') . "files/" . $filename;
})->bind("upload");

$commonController->get('/users', function(Request $request) use ($app) {
        $users = $app['userService']->getAllUsers();
		$isAdmin = $app['campagneService']->isAdmin();
	return $app->render('user_list.html.twig', ['users' => $users,'isAdmin' => $isAdmin]);
})->bind("user_list");


$app->mount('/', $commonController);

?>
