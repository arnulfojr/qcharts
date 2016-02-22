<?php


namespace QCharts\CoreBundle\ResultFormatter;


interface ResultsFormatterFactoryInterface
{

    /**
     * @return mixed
     */
    public function getFormatType();

    /**
     * @param $formatType
     * @internal param $chartType
     */
    public function setFormatType($formatType);

    /**
     * @return mixed
     */
    public function getFormatter();
}