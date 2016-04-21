<?php

namespace QCharts\FrontendBundle;

use QCharts\FrontendBundle\DependencyInjection\FrontendExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontendBundle extends Bundle
{
    // Make FrontendBundle a child from FOSUserBundle to override the forms
    public function getParent()
    {
        return "";
    }

    /**
     * @return FrontendExtension
     */
    public function getContainerExtension()
    {
        return new FrontendExtension();
    }

}