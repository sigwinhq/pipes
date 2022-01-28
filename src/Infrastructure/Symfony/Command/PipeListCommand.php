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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PipeListCommand extends Command
{
    private const COMMAND_NAME = 'pipes:list';

    protected static $defaultName = self::COMMAND_NAME;
    private PipeManager $manager;

    public function __construct(PipeManager $manager)
    {
        parent::__construct(self::COMMAND_NAME);

        $this->manager = $manager;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Sigwin Pipes');

        $rows = $this->manager->findAll();

        $rendered = false;
        $table = $style->createTable();
        $table->setHeaderTitle('Available Pipes');
        $table->setHeaders(['PK', 'SK', 'Name']);
        foreach ($rows as $row) {
            $table->appendRow([$row['PK'], $row['SK'], $row['name']]);
            $rendered = true;
        }

        if ( ! $rendered) {
            $style->warning('No Pipes currently defined');

            return 1;
        }

        return 1;
    }
}
