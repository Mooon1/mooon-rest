<?php

namespace Mooon\Rest;

use Mooon\Rest\DependencyInjection\MooonRestExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MooonRestBundle
 */
class MooonRestBundle extends Bundle
{
    /**
     * @return MooonRestBundle
     */
    public function getContainerExtension(): MooonRestExtension
    {
        return new MooonRestExtension();
    }
}