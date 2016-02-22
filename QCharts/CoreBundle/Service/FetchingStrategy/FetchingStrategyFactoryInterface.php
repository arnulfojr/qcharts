<?php


namespace QCharts\CoreBundle\Service\FetchingStrategy;


interface FetchingStrategyFactoryInterface
{
    /**
     * @param $mode
     * @param null $snapshot
     * @return mixed
     */
    public function createStrategy($mode, $snapshot = null);
}