<?php

namespace App;

use Symfony\Component\Notifier\ChatterInterface;

class EventExchange
{
    /** @var iterable<EventConverter> */
    private iterable $converters;
    
    private ChatterInterface $chatter;

    public function __construct(iterable $converters, ChatterInterface $chatter)
    {
        $this->converters = $converters;
        $this->chatter = $chatter;
    }
    
    public function receive(Event $event): void
    {
        foreach ($this->converters as $converter) {
            if ($converter->supports($event)) {
                $message = $converter->convert($event);
                
                $this->chatter->send($message);
                
                return;
            }
        }
        
        throw new \UnexpectedValueException('Unexpected event');
    }
}
