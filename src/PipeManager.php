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

namespace App;

use App\Repository\PipeRepository;

final class PipeManager
{
    private PipeRepository $repository;
    private Storage $storage;

    public function __construct(PipeRepository $repository, Storage $storage)
    {
        $this->repository = $repository;
        $this->storage = $storage;
    }

    public function create(string $name): void
    {
        $this->repository->create($name);
    }

    public function remove(string $identifier): void
    {
        $this->repository->remove($identifier);
    }

    /**
     * @return iterable<array{PK: string, SK: string, name: string}>
     */
    public function findAll(): iterable
    {
        return $this->repository->findAll();
    }

    public function createStorage(): void
    {
        $this->storage->create();
    }
}
