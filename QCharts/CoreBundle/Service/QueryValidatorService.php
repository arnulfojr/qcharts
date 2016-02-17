<?php

namespace QCharts\CoreBundle\Service;

use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Repository\DynamicRepository;
use QCharts\CoreBundle\Repository\QueryRepository;
use QCharts\CoreBundle\ResultFormatter\OneDimensionTableFormatter;
use QCharts\CoreBundle\Validation\ChartValidationFactory;
use QCharts\CoreBundle\Validation\SyntaxSemanticValidationFactory;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;
use QCharts\CoreBundle\Validation\Validator\ExistenceValidator;
use Symfony\Bridge\Monolog\Logger;


class QueryValidatorService
{
	/** @var QueryRepository  */
	private $repository;
    /** @var QuerySyntaxService $querySyntax */
    private $querySyntax;
    /** @var array $chartTypes */
    private $chartTypes;
    /** @var DynamicRepository $dynamicRepo */
    private $dynamicRepo;
    /** @var LimitsService $limits */
    private $limits;

    /**
     * @param QueryRepository $repo
     * @param DynamicRepository $dynamicRepository
     * @param QuerySyntaxService $syx
     * @param array $chartTypes
     * @param LimitsService $limits
     */
    public function __construct(
        QueryRepository $repo,
        DynamicRepository $dynamicRepository,
        QuerySyntaxService $syx,
        array $chartTypes,
        LimitsService $limits)
    {
        $this->dynamicRepo = $dynamicRepository;
		$this->repository = $repo;
        $this->querySyntax = $syx;
        $this->chartTypes = $chartTypes;
        $this->limits = $limits;
	}

    /**
     * @param $queryString
     * @param $connectionName
     */
    public function isValidQuery($queryString, $connectionName)
    {
        $this->validateConnection($connectionName);
        $this->dynamicRepo->setUp($connectionName);

        $formatter = new OneDimensionTableFormatter();
        $this->limits->addLimit("table_names", $formatter->formatResults($this->dynamicRepo->getAllTableNames()));

        $factory = new SyntaxSemanticValidationFactory();

        $factory->setLimits($this->limits->getLimits());
        $factory->registerValidators();
        $validators = $factory->getValidators();

        foreach ($validators as $validator)
        {
            /** @var ValidatorInterface $validator */
            $validator->setObject($queryString);
            $validator->validate();
        }
    }

    /**
     * @param $query
     * @param array $configurations
     * @return array
     * @throws \QCharts\CoreBundle\Exception\DatabaseException
     */
    public function validateQueryExecution($query, array $configurations)
    {
        $offset = $configurations["offset"];
        $query = $this->querySyntax->getLimitedQuery($query, $this->getMaxRows($configurations["rows"]), $offset);

        $this->validateConnection($configurations["connection"]);

        $this->dynamicRepo->setUp($configurations["connection"]);
        $results = $this->dynamicRepo->execute($query);

        $duration = $this->dynamicRepo->getExecutionDuration();

        $configurations["validation"] = [
            "duration"=>$duration,
            "results"=>$results
        ];

        $validators = $this->getValidators($configurations);

        foreach ($validators as $name=>$validator)
        {
            /** @var ValidatorInterface $validator */
            $validator->validate();
        }

        return $results;
    }

    /**
     * @param array $configurations
     * @return array
     */
    protected function getValidators(array $configurations)
    {
        $validatorFactory = new ChartValidationFactory();

        $this->limits->addLimit('Existence', $this->chartTypes);
        $this->limits->addLimit('Offset', []);
        $this->limits->addLimit('PieCompability', $configurations["validation"]["results"]);
        $this->limits->addLimit('NumericList', $configurations["chartType"]);
        $this->limits->addLimit('TimeExecution', $this->getMaxTime($configurations["time"], $configurations["connection"]));
        $this->limits->addLimit('RowLimit', $this->getMaxRows($configurations["rows"]));
        $this->limits->addLimit('ExecutionTimeConfiguration', $this->getMaxTime($configurations["time"], $configurations["connection"]));

        $validatorFactory->setLimits($this->limits->getLimits());
        $validatorFactory->registerValidators();

        $validatorFactory->addLimitsToValidators();
        $validators = $validatorFactory->getValidators();

        //TODO: refactor this!
        /** @var ValidatorInterface $validator */
        $validator = $validators["PieCompability"];
        $validator->setObject(["results"=>$configurations["validation"]["results"], "chartType"=>$configurations["chartType"]]);
        $validator = $validators["Existence"];
        $validator->setObject($configurations["chartType"]);
        $validator = $validators["TimeExecution"];
        $validator->setObject($configurations["validation"]["duration"]);
        $validator = $validators["RowLimit"];
        $validator->setObject($configurations["rows"]);
        $validator = $validators["Offset"];
        $validator->setObject($configurations["offset"]);
        $validator = $validators["ExecutionTimeConfiguration"];
        $validator->setObject($configurations["time"]);
        $validator = $validators["NumericList"];
        $validator->setObject($configurations["validation"]["results"]);
        $validator = $validators["CronExpression"];
        $validator->setObject($configurations["cronExpression"]);

        return $validators;
    }

    /**
     * @param $connectionName
     * @return bool
     * @throws ValidationFailedException
     * @throws \QCharts\CoreBundle\Exception\TypeNotValidException
     */
    public function validateConnection($connectionName)
    {
        $validator = new ExistenceValidator();
        $validator->setKeyComparison('Connection');
        $validator->setLimits(["Connection"=>$this->dynamicRepo->getConnectionNames()]);
        $validator->setObject($connectionName);

        return $validator->validate();
    }

    /**
     * @param int $rowLimit
     * @return int
     */
    public function getMaxRows($rowLimit = 0)
    {
        $configLimits = $this->limits->getLimits();
        if ($rowLimit > 0)
        {
            if ($rowLimit > $configLimits["row"])
            {
                return $configLimits["row"];
            }
            return $rowLimit;
        }
        return $configLimits["row"];
    }

    /**
     * @param int $timeLimit
     * @param string $connectionName
     * @return bool|int|number
     */
    public function getMaxTime($timeLimit = 0, $connectionName = 'default')
    {
        $this->validateConnection($connectionName);
        $this->dynamicRepo->setUp($connectionName);

        $time = $this->dynamicRepo->isMaxExecutionSet();
        $configLimits = $this->limits->getLimits();

        if ($time)
        {
            if ($timeLimit > 0 && $time > $timeLimit)
            {
                return $timeLimit;
            }

            return $time;
        }

        if ($timeLimit > 0)
        {
            if ($timeLimit > $configLimits["time"])
            {
                return $configLimits["time"];
            }
            return $timeLimit;
        }

        return $configLimits["time"];
    }

    /**
     * @param $query
     * @param int $numericLimit
     * @return mixed|string
     */
    public function getLimitedQuery($query, $numericLimit = 0)
    {
        $query = $this->querySyntax->getLimitedQuery($query, $numericLimit);
        return $query;
    }

    /**
     * @return float|string
     * @throws \QCharts\CoreBundle\Exception\DatabaseException
     */
    public function getExecutionDuration()
    {
        $duration = $this->dynamicRepo->getExecutionDuration();
        $duration = number_format($duration, 7, '.', ',');
        return $duration;
    }

}
