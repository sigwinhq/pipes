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

use App\Infrastructure\Symfony\Kernel;

require_once \dirname(__DIR__).'/vendor/autoload_runtime.php';

return
    /**
     * @param array{APP_ENV: string, APP_DEBUG: bool|int} $context
     */
    static function (array $context): Kernel {
        return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    };
