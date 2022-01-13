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

use AsyncAws\DynamoDb\DynamoDbClient;
use AsyncAws\DynamoDb\Input\PutItemInput;
use AsyncAws\DynamoDb\ValueObject\AttributeValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PipeCreateCommand extends Command
{
    private const COMMAND_NAME = 'pipes:pipe:create';

    protected static $defaultName = self::COMMAND_NAME;

    private DynamoDbClient $client;
    private string $tableName;

    public function __construct(DynamoDbClient $client, string $tableName)
    {
        $this->client = $client;
        $this->tableName = $tableName;

        parent::__construct(self::COMMAND_NAME);
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

        $this->client->putItem(new PutItemInput([
            'TableName' => $this->tableName,
            'Item' => [
                'PK' => new AttributeValue(['S' => $name]),
                'SK' => new AttributeValue(['S' => $name]),
                'name' => new AttributeValue(['S' => $name]),
                // 'name' => new AttributeValue(['S' => $input->getArgument('name')]),
            ],
        ]));

        return 0;
    }
}
