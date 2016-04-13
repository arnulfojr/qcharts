<?php

namespace QCharts\CoreBundle\Service\FetchingStrategy;


use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\OffLimitsException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Service\QueryValidatorService;
use QCharts\CoreBundle\Service\ServiceInterface\DynamicDependencies;

class LiveStrategy extends DynamicDependencies implements FetchingStrategyInterface
{
    const DEPENDENCY_NAME = "QueryValidator";

    /**
     * @param QueryRequest $qr
     * @return array
     * @throws TypeNotValidException
     * @throws ValidationFailedException
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
            try
            {
                $dependencies = $this->getDependencies();
                /** @var QueryValidatorService $queryValidator */
                $queryValidator = $dependencies[LiveStrategy::DEPENDENCY_NAME];
                $query = $qr->getQuery()->getQuery();
                $queryValidator->isValidQuery($query, $configurations["connection"]);
                $results = $queryValidator->validateQueryExecution($query, $configurations);

                return $results;
            }
            catch (OffLimitsException $e)
            {
                $exception = new ValidationFailedException("Query validation has failed. {$e->getMessage()}", $e->getCode(), $e);
                throw $exception;
            }
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