<?php
namespace jdRoll\controller;

use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function __construct($db, $session) 
    {
        $this->db = $db;
        $this->session = $session;
    }

    public function indexAction() 
    {
        return new JsonResponse("Index de l'API REST");
    }

    public function loginAction() {

    }

    public function logoutAction() {
        
    }
}