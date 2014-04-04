<?php
namespace jdRoll\controller;

use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function searchAction($id) 
    {
        $result = $this->campaign->getMyCampagnes($id);
        return new JsonResponse($result);
    }

}