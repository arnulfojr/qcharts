<?php

namespace QCharts\CoreBundle\Service\FetchingStrategy;

use QCharts\CoreBundle\Entity\QueryRequest;

interface FetchingStrategyInterface
{
    /**
     * @param QueryRequest $queryRequest
     * @return array
     */
    public function getResults(QueryRequest $queryRequest);

    /**
     * @return string
     */
    public function getDuration();
}