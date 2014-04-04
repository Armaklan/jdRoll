<?php
namespace jdRoll\controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Control session REST method ( login, logout, ...)
 *
 * @package SessionController
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */
class SessionController 
{

    private $db;
    private $session;
    private $logger;

    public function __construct($session, $logger, $user) 
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->user = $user;
    }

    public function indexAction() 
    {
        return new JsonResponse("Index de l'API REST");
    }

    public function loginAction(Request $request) {
        try {
            $payload = json_decode($request->getContent());
            $user = $this->user->login($payload->username, $payload->password);
            return new JsonResponse($user);
        } catch (\Exception $e) {
            $this->logger->addInfo("Not authorized for : " . $payload->username);
            return new JsonResponse($e->getMessage(), 403);
        }
    }

    public function logoutAction() {
        
    }
}