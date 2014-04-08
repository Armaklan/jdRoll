<?php
namespace jdRoll\controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    private $absence;

    public function __construct($session, $logger, $user, $absence) 
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->user = $user;
        $this->absence = $absence;
    }

    public function searchAction(Request $request) 
    {
        try {
            $user = [];

            if($request->get('isLastSubscribe', false) == true) {
                $user = $this->user->getLastSubscribe();
            } else if($request->get('isRecentConnected', false) == true) {
                $user = $this->user->getConnectedIn24H();
            } else if($request->get('isMissing', false) == true) {
                $user = $this->absence->getCurrentAbsence();
            } else if($request->get('hasBirthday', false) == true) {
                $user = $this->user->getCurrentBirthDay();
            } else {
                $user = $this->user->getAllUsers();
            }

            return new JsonResponse($user);
        } catch (\Exception $e){
            return new JsonResponse(null);
        }
    }

    public function currentAction() 
    {
        try {
            $user = $this->user->getCurrent();
            return new JsonResponse($user);
        } catch (\Exception $e){
            return new JsonResponse(null);
        }
    }

}