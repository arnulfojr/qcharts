<?php

namespace QCharts\FrontendBundle\Twig;

class FetchingModesFilter extends \Twig_Extension
{

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('mode', [$this, 'modeFilter'])
        ];
    }

    /**
     * @param $number
     * @return string
     */
    public function modeFilter($number)
    {
        $modes = ["Live", "Snapshot Mode", "Time Machine"];
        return $modes[$number];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "fetching_modes_extension";
    }
}