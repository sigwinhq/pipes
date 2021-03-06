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

if (file_exists(\dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php')) {
    /** @psalm-suppress MissingFile */
    require \dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php';
}
