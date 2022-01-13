<?php

namespace App\Infrastructure\Symfony;

use Bref\SymfonyBridge\BrefKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

class Kernel extends BrefKernel
{
    use MicroKernelTrait;
}
