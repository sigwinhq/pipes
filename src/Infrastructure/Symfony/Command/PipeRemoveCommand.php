<?php

declare(strict_types=1);

/*
 * This file is part of the Sigwin Pipes project.
 *
 * (c) sigwin.hr
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Infrastructure\Symfony\Command;

use App\PipeManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PipeRemoveCommand extends Command
{
    private const COMMAND_NAME = 'pipes:delete';

    protected static $defaultName = self::COMMAND_NAME;
    private PipeManager $manager;

    public function __construct(PipeManager $manager)
    {
        parent::__construct(self::COMMAND_NAME);

        $this->manager = $manager;
    }

    protected function configure(): void
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'ID of the pipe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var string $identifier
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $identifier = $input->getArgument('identifier');

        $this->manager->remove($identifier);

        return 0;
    }
}
