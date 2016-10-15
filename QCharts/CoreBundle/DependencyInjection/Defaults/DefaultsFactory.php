<?php

namespace QCharts\CoreBundle\DependencyInjection\Defaults;

use QCharts\CoreBundle\DependencyInjection\Defaults\Values\ChartDefaults;
use QCharts\CoreBundle\DependencyInjection\Defaults\Values\PathsDefaults;
use QCharts\CoreBundle\DependencyInjection\Defaults\Values\UrlsDefaults;

/**
 * Class Factory
 * @package QCharts\CoreBundle\DependencyInjection\Defaults
 */
class DefaultsFactory
{
    /**
     *
     * Register here the Defaults
     *
     * @param string $valueName
     * @return mixed
     */
    static function getValues($valueName)
    {
        switch ($valueName)
        {
            case "charts":
                return ChartDefaults::getValue();
            break;
            case "urls":
                return UrlsDefaults::getValue();
            break;
            case "paths":
                return PathsDefaults::getValue();
        }
    }



}