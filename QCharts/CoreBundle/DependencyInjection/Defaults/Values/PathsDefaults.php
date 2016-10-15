<?php

namespace QCharts\CoreBundle\DependencyInjection\Defaults\Values;

/**
 *
 * Contains the default values of the paths
 *
 * Class PathsDefaults
 * @package QCharts\CoreBundle\DependencyInjection\Defaults\Values
 */
class PathsDefaults implements DefaultsInterface
{
    /**
     * Returns the default path for snapshots, static path "/"
     * @return array
     */
    static function getValue()
    {
        return [
            "paths" => [
                "snapshots" => "/",
            ],
        ];
    }

}