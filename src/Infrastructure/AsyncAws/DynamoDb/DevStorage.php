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

namespace App\Infrastructure\AsyncAws\DynamoDb;

use App\Storage;
use AsyncAws\DynamoDb\DynamoDbClient;
use AsyncAws\DynamoDb\Exception\ResourceNotFoundException;
use AsyncAws\DynamoDb\Input\CreateTableInput;
use AsyncAws\DynamoDb\Input\DeleteTableInput;
use AsyncAws\DynamoDb\ValueObject\AttributeDefinition;
use AsyncAws\DynamoDb\ValueObject\KeySchemaElement;
use AsyncAws\DynamoDb\ValueObject\ProvisionedThroughput;

final class DevStorage implements Storage
{
    private string $tableName;
    private DynamoDbClient $client;

    public function __construct(string $tableName, DynamoDbClient $client)
    {
        $this->tableName = $tableName;
        $this->client = $client;
    }

    public function create(): void
    {
        try {
            $this->client->deleteTable(new DeleteTableInput([
                'TableName' => $this->tableName,
            ]));
        } catch (ResourceNotFoundException) {
            // ignored
        }

        $this->client->createTable(new CreateTableInput([
            'TableName' => $this->tableName,
            'AttributeDefinitions' => [
                new AttributeDefinition(['AttributeName' => 'PK', 'AttributeType' => 'S']),
                new AttributeDefinition(['AttributeName' => 'SK', 'AttributeType' => 'S']),
            ],
            'KeySchema' => [
                new KeySchemaElement(['AttributeName' => 'PK', 'KeyType' => 'HASH']),
                new KeySchemaElement(['AttributeName' => 'SK', 'KeyType' => 'RANGE']),
            ],
            'ProvisionedThroughput' => new ProvisionedThroughput([
                'ReadCapacityUnits' => '100',
                'WriteCapacityUnits' => '100',
            ]),
        ]));
    }
}
