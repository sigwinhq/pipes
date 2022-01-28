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

namespace App\Repository;

interface PipeRepository
{
    public function create(string $name): void;

    /**
     * @return iterable<array{PK: string, SK: string, name: string}>
     */
    public function findAll(): iterable;

    public function remove(string $identifier): void;
}
