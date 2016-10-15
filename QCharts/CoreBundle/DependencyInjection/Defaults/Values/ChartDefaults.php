<?php

namespace QCharts\CoreBundle\DependencyInjection\Defaults\Values;

/**
 *
 * Contains the default values of the chart types
 *
 * Class ChartDefaults
 * @package QCharts\CoreBundle\DependencyInjection\Defaults\Values
 */
class ChartDefaults implements DefaultsInterface
{
    /**
     * Returns the defaults for the Chart Types supported
     * @return array
     */
    static function getValue()
    {
        return [
            "line" => "Line",
            "spline" => "SpLine",
            "area" => "Area",
            "pie" => "Pìe Chart",
            "bar" => "Bar Chart",
            "column" => "Column Chart",
            "table" => "Table of Contents",
            "polar_line" => "Polar Lines",
            "polar_area" => "Polar Area",
            "polar_spline" => "Polar SpLine",
            "polar_bar" => "Polar Bar",
            "polar_column" => "Polar Column"
        ];
    }
}