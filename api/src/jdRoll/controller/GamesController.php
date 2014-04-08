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
class GamesController 
{

    private $campaign;
    private $session;
    private $logger;

    public function __construct($session, $logger, $campaign) 
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->campaign = $campaign;
    }

    public function searchAction(Request $request) 
    {
        $result= [];
        $this->logger->addInfo("Debut search action");
        if($request->get('enlistmentOpen', false) == true) {
            $result = $this->campaign->getOpenCampagne();
        } else {
            $userId = $request->get('userId');
            $result = $this->campaign->getMyCampagnes($userId);
        }
        return new JsonResponse($result);
    }

}