<?php

namespace QCharts\ApiBundle;

use QCharts\ApiBundle\DependencyInjection\ApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiBundle extends Bundle
{
    /**
     * @return ApiExtension
     */
    public function getContainerExtension()
    {
        return new ApiExtension();
    }
}
