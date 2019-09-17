<?php

namespace Mooon\Rest\Controller\Health;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HealthController
 *
 * Just a basic helper class to test availability of backend services.
 */
class HealthController
{
    /**
     * @Route("/mooon/rest/health", methods={"GET"})
     */
    public function readHealth()
    {
        return [
            "health" => 100,
        ];
    }
}