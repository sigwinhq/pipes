<?php

namespace App\EventConverter;

use App\Event;
use App\EventConverter;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackImageBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\MessageInterface;

class GithubEventConverter implements EventConverter
{
    public function supports(Event $event): bool
    {
        return isset($event->metadata['x-gitlab-event']);
    }

    public function convert(Event $event): MessageInterface
    {
        $options = new SlackOptions();
        $options
            // TODO: figure out correct channel from project
            ->recipient('pipes-test')
            ->block((new SlackHeaderBlock('Hello')))
            ->block((new SlackSectionBlock())
                ->text('*Pipeline has failed*', true)
            )
            ->block((new SlackSectionBlock())
                ->field('*Branch*', true)
                ->field('*Commit*', true)
            )
            ->block((new SlackSectionBlock())
                ->field('*Failed stage*', true)
                ->field('*Failed job*', true)
            );
        
        $message = new ChatMessage('Github event: *'. $event->metadata['x-gitlab-event'][0] .'*', $options);
        
        return $message;
    }
}
