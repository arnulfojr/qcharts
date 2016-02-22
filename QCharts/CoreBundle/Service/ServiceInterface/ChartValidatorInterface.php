<?php


namespace QCharts\CoreBundle\Service\ServiceInterface;


interface ChartValidatorInterface
{
    /**
     * @param $chartType
     * @return mixed
     */
    public function chartTypeIsValid($chartType);

    /**
     * @param array $rawArray
     * @return bool
     */
    public function resultsArePieCompatible(array $rawArray);

    /**
     * @param array $array
     * @param string $chartType
     * @return bool
     */
    public function resultsAreNumeric(array $array, $chartType);

    /**
     * @return array
     */
    public function getChartTypes();
}