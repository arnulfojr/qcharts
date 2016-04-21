<?php

namespace QCharts\CoreBundle;

use QCharts\CoreBundle\DependencyInjection\CoreExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreBundle extends Bundle
{
    /**
     * @return CoreExtension
     */
    public function getContainerExtension()
    {
        return new CoreExtension();
    }
}
