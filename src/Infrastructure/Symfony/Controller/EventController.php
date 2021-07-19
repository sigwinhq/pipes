<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Event;
use App\EventExchange;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController
{
    private EventExchange $receiver;

    public function __construct(EventExchange $receiver)
    {
        $this->receiver = $receiver;
    }
    
    /**
     * @Route("/event", methods={"POST"})
     */
    public function __invoke(Request $request): Response
    {
        $event = new Event;
        $event->metadata = $request->headers->all();
        $event->payload = (string) $request->getContent();
        
        $this->receiver->receive($event);
        
        return new Response('OK');
    }
}
