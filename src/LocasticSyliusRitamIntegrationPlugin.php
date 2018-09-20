<?php

declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class LocasticSyliusRitamIntegrationPlugin extends Bundle
{
    use SyliusPluginTrait;
}
