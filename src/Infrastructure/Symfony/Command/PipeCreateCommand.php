<?php

namespace App\Infrastructure\Symfony\Command;

use AsyncAws\DynamoDb\DynamoDbClient;
use AsyncAws\DynamoDb\Input\PutItemInput;
use AsyncAws\DynamoDb\ValueObject\AttributeValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PipeCreateCommand extends Command
{
    protected static $defaultName = 'pipes:pipe:create';
    
    private DynamoDbClient $client;
    private string $tableName;

    public function __construct(DynamoDbClient $client, string $tableName)
    {
        $this->client = $client;
        $this->tableName = $tableName;
        
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the pipe');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->client->putItem(new PutItemInput([
            'TableName' => $this->tableName,
            'Item' => [
                'PK' => new AttributeValue(['S' => $input->getArgument('name')]),
                'SK' => new AttributeValue(['S' => $input->getArgument('name')]),
                'name'=> new AttributeValue(['S' => $input->getArgument('name')]),
                // 'name' => new AttributeValue(['S' => $input->getArgument('name')]),
            ],
        ]));
        
        return 0;
    }
}
