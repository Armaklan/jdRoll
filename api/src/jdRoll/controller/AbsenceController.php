<?php
namespace jdRoll\controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Control user REST API
 *
 * @package CampaignController
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */
class AbsenceController 
{

    private $campaign;
    private $session;
    private $logger;

    public function __construct($session, $logger, $absence) 
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->absence = $absence;
    }

    public function searchAction(Request $request) 
    {
        $result= [];
        $userId = $request->get('userId');
        $result = $this->absence->getAllAbsence($userId);
        return new JsonResponse($result);
    }

}