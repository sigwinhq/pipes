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

use App\Repository\PipeRepository as PipeRepositoryInterface;
use AsyncAws\DynamoDb\DynamoDbClient;
use AsyncAws\DynamoDb\Input\PutItemInput;
use AsyncAws\DynamoDb\ValueObject\AttributeValue;

final class PipeRepository implements PipeRepositoryInterface
{
    private string $tableName;
    private DynamoDbClient $client;

    public function __construct(string $tableName, DynamoDbClient $client)
    {
        $this->tableName = $tableName;
        $this->client = $client;
    }

    public function create(string $name): void
    {
        $this->client->putItem(new PutItemInput([
            'TableName' => $this->tableName,
            'Item' => [
                'PK' => new AttributeValue(['S' => $name]),
                'SK' => new AttributeValue(['S' => $name]),
                'name' => new AttributeValue(['S' => $name]),
            ],
        ]));
    }
}
