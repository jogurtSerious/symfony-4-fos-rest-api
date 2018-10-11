<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthcheckController extends FOSRestController
{
    /**
     * @Rest\Get(path="/ping")
     */
    public function getAction()
    {
        return new JsonResponse(
            'pong'
        );
    }
}
