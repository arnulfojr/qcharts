<?php

namespace QCharts\CoreBundle\ResultFormatter;


class HCPolarFormatter extends HCUniversalFormatter
{
    /**
     * @return array
     */
    public function getChartConfig()
    {
        return [
            "polar"=>true,
            "type"=>$this->getChartType($this->chartType)
        ];
    }

    /**
     * @param $complexType
     * @return mixed
     */
    public static function getChartType($complexType)
    {
        $regex = '/polar_/';
        $result = preg_replace($regex, '', $complexType);
        return $result;
    }

}