<?php

namespace App\Infrastructure\Symfony\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class NotifyCommand extends Command
{
    protected static $defaultName = 'pipes:notify';
    
    private ChatterInterface $chatter;

    public function __construct(ChatterInterface $chatter)
    {
        parent::__construct(self::$defaultName);
        
        $this->chatter = $chatter;
    }
    
    protected function configure()
    {
        $this
            ->addArgument('message', InputArgument::REQUIRED, 'Message to send')
            ->addOption('recipient', 'r', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Message recipient');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = $input->getArgument('message');
        if ([] === $recipients = $input->getOption('recipient')) {
            $this->chatter->send(new ChatMessage($message));

            return 0;
        }

        foreach ($recipients as $recipient) {
            $options = new SlackOptions();
            $options->recipient($recipient);

            $chatMessage = new ChatMessage($message, $options);
            $this->chatter->send($chatMessage);
        }
        
        return 0;
    }
}
