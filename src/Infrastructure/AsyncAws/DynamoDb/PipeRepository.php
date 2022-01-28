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
use AsyncAws\DynamoDb\Input\DeleteItemInput;
use AsyncAws\DynamoDb\Input\PutItemInput;
use AsyncAws\DynamoDb\Input\ScanInput;
use AsyncAws\DynamoDb\ValueObject\AttributeValue;
use Symfony\Component\Uid\Ulid;

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
        $this->client->putItem(new PutItemInput($this->createRequest([
            'Item' => [
                'PK' => new AttributeValue(['S' => Ulid::generate()]),
                'SK' => new AttributeValue(['S' => 'foo']),
                'name' => new AttributeValue(['S' => $name]),
            ],
        ])));
    }

    public function findAll(): iterable
    {
        // TODO: should be a query()
        $rows = $this->client->scan(new ScanInput($this->createRequest()));

        foreach ($rows as $row) {
            // TODO: cleanup here once we have unmarshall
            yield [
                'PK' => $row['PK']->getS() ?? '',
                'SK' => $row['SK']->getS() ?? '',
                'name' => $row['name']->getS() ?? '',
            ];
        }
    }

    public function remove(string $identifier): void
    {
        $this->client->deleteItem(new DeleteItemInput($this->createRequest([
            'Key' => [
                'PK' => new AttributeValue(['S' => $identifier]),
                'SK' => new AttributeValue(['S' => 'foo']),
            ],
        ])));
    }

    private function createRequest(array $request = []): array
    {
        return array_merge(['TableName' => $this->tableName], $request);
    }
}
