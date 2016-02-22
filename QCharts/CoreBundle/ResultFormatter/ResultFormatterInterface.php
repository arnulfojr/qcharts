<?php


namespace QCharts\CoreBundle\ResultFormatter;


interface ResultFormatterInterface
{

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function formatResults(array $rawResults);

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function getSeries(array $rawResults);

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function getYAxis(array $rawResults);

}