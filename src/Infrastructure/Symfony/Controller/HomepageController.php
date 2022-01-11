<?php

namespace App\Infrastructure\Symfony\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController
{
    #[Route("/", name: "homepage")]
    public function __invoke(): Response
    {
        return new Response('Sigwin Pipes');
    }
}
