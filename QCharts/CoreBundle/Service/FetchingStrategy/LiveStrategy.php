<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/3/16
 * Time: 4:43 PM
 */

namespace QCharts\CoreBundle\Service\FetchingStrategy;


use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Service\QueryValidatorService;
use QCharts\CoreBundle\Service\ServiceInterface\DynamicDependencies;

class LiveStrategy extends DynamicDependencies implements FetchingStrategyInterface
{
    const DEPENDENCY_NAME = "QueryValidator";
    /**
     * @param QueryRequest $qr
     * @return array
     * @throws TypeNotValidException
     */
    public function getResults(QueryRequest $qr)
    {
        // if the query request is cached then call the cached version!
        $configurations = [
            "time"=>$qr->getConfig()->getExecutionLimit(),
            "rows"=>$qr->getConfig()->getQueryLimit(),
            "chartType"=>$qr->getConfig()->getTypeOfChart(),
            "offset"=>$qr->getConfig()->getOffset(),
            "connection"=>$qr->getConfig()->getDatabaseConnection(),
            "cronExpression"=>$qr->getCronExpression()
        ];

        if ($this->hasDependency(LiveStrategy::DEPENDENCY_NAME))
        {
            /** @var QueryValidatorService $queryValidator */
            $queryValidator = $this->getDependencies()[LiveStrategy::DEPENDENCY_NAME];
            $query = $qr->getQuery()->getQuery();
            $queryValidator->isValidQuery($query, $configurations["connection"]);
            $results = $queryValidator->validateQueryExecution($query, $configurations);

            return $results;
        }

        throw new TypeNotValidException(ExceptionMessage::DEPENDENCY_NOT_AVAILABLE(", QueryValidator was absent"), 500);
    }

    /**
     * @return float|string
     */
    public function getDuration()
    {
        if ($this->hasDependency(LiveStrategy::DEPENDENCY_NAME))
        {
            /** @var QueryValidatorService $queryValidator */
            $queryValidator = $this->getDependencies()[LiveStrategy::DEPENDENCY_NAME];
            return $queryValidator->getExecutionDuration();
        }

        return "0";
    }
}