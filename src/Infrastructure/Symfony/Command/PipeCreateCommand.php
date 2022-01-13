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

final class PipeCreateCommand extends Command
{
    private const COMMAND_NAME = 'pipes:pipe:create';

    protected static $defaultName = self::COMMAND_NAME;
    private PipeManager $manager;

    public function __construct(PipeManager $manager)
    {
        parent::__construct(self::COMMAND_NAME);

        $this->manager = $manager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the pipe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var string $name
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $name = $input->getArgument('name');

        $this->manager->create($name);

        return 0;
    }
}
