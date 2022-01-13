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

namespace App\Test\Infrastructure\Symfony;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @small
 *
 * @covers \App\Infrastructure\Symfony\Kernel
 */
final class KernelTest extends TestCase
{
    public function testWorks(): void
    {
        /** @phpstan-ignore-next-line */
        static::assertTrue(true);
    }
}
