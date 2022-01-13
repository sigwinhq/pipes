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

    public function __construct(PipeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(string $name): void
    {
        $this->repository->create($name);
    }
}
