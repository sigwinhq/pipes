<?php

namespace App\Infrastructure\Symfony\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController
{
    /**
     * @Route("/event", methods={"POST"})
     */
    public function __invoke(): Response
    {
        return new Response('OK');
    }
}
