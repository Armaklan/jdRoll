<?php
namespace jdRoll\controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Control user REST API
 *
 * @package UserController
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */
class UserController 
{

    private $user;
    private $session;
    private $logger;

    public function __construct($session, $logger, $user) 
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->user = $user;
    }

    public function currentAction() 
    {
        try {
            $user = $this->user->getCurrent();
            return new JsonResponse($user);
        } catch (\Exception $e){
            return new JsonResponse(null, 403);
        }
    }

}