<?php

namespace App;

use Symfony\Component\Notifier\Message\MessageInterface;

interface EventConverter
{
    public function supports(Event $event): bool;
    
    public function convert(Event $event): MessageInterface;
}
