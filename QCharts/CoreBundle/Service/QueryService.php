<?php

namespace QCharts\CoreBundle\Service;

use Doctrine\ORM\EntityRepository;
use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Entity\User\QChartsSubjectInterface;
use QCharts\CoreBundle\Exception\DatabaseException;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\OffLimitsException;
use QCharts\CoreBundle\Exception\ParameterNotPassedException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Exception\OverlappingException;
use QCharts\CoreBundle\Repository\DynamicRepository;
use QCharts\CoreBundle\Repository\QueryRepository;
use QCharts\CoreBundle\Service\ServiceInterface\QueryServiceInterface;
use Doctrine\DBAL\DBALException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use \SqlFormatter;
use Symfony\Component\HttpFoundation\ParameterBag;

class QueryService implements QueryServiceInterface
{

    /** @var QueryRepository $repository */
    private $repository = null;
    /** @var \QCharts\CoreBundle\Service\QueryValidatorService|null s */
    private $queryValidator = null;
    /** @var null|DynamicRepository $dynamicRepo */
    private $dynamicRepo = null;

    /**
     * QueryService constructor.
     * @param EntityRepository $repo
     * @param DynamicRepository $dynamicRepository
     * @param QueryValidatorService $val
     */
    public function __construct(
        EntityRepository $repo,
        DynamicRepository $dynamicRepository,
        QueryValidatorService $val
        )
	{
        $this->repository = $repo;
        $this->dynamicRepo = $dynamicRepository;
        $this->queryValidator = $val;
    }

    /**
     * @return null|QueryValidatorService
     */
    public function getQueryValidator()
    {
        return $this->queryValidator;
    }

    /**
     * Returns all queries ordered by date
     * @return array
     */
    public function getAllQueries()
    {
        $allQueries = $this->repository->findAllOrderedByDateCreated();
        return $allQueries;
    }

    /**
     * @return array
     */
    public function getPreFetchedQueries()
    {
        return $this->repository->getCachedQueries();
    }

    /**
     * @return array
     */
    public function getTimeMachinedQueries()
    {
        return $this->repository->getTimeMachineQueries();
    }

    /**
     * @param null $queryId
     * @return array|QueryRequest
     * @throws InstanceNotFoundException
     */
    public function getQueries($queryId = null)
    {
        if (is_null($queryId) || $queryId < 0)
        {
            //return all
            $queryRequest = $this->getAllQueries();
        }
        else
        {
            $queryRequest = $this->getQueryRequestById($queryId);
        }
        return $queryRequest;
    }

    /**
     * @param number $id
     * @return QueryRequest
     * @throws InstanceNotFoundException
     */
    public function getQueryRequestById($id)
    {
        if (is_null($id))
        {
            throw new InstanceNotFoundException("No valid Id was given or found", 404);
        }

        /** @var QueryRequest $query */
        $query = $this->repository->find($id);

        if (!$query)
        {
            throw new InstanceNotFoundException("Requested Query not found", 404);
        }
        return $query;
    }

    /**
     * @param $id
     * @return mixed
     * @throws InstanceNotFoundException
     */
    public function getQueryFromQueryRequest($id)
    {
    	$query = $this->getQueryRequestById($id);
    	return $query->getQuery();
    }

    /**
     * @param null $directoryId
     * @return array
     */
    public function getQueriesInDirectory($directoryId = null)
    {
        $queries = $this->repository->getQueriesInDirectory($directoryId);
        return $queries;
    }

    /**
     * @param Form $form
     * @param QChartsSubjectInterface $user
     * @return array
     * @throws OffLimitsException
     * @throws ParameterNotPassedException
     * @throws TypeNotValidException
     * @throws ValidationFailedException
     */
    public function add(Form $form, QChartsSubjectInterface $user)
    {
        /** @var QueryRequest $queryRequest */
        /** @var Form $form */

        $queryRequest = $form->getData();
        $queryRequest->setCreatedBy($user);
        $queryRequest->setModifiedLastBy($user);

        $query = $queryRequest->getQuery();
        $chartConfig = $queryRequest->getConfig();

        $rawQueryString = $query->getQuery();
        $htmlQuery = SqlFormatter::format($rawQueryString);
        $query->setQueryHTML($htmlQuery);
        $results = [];

        try
        {
            $this->queryValidator->isValidQuery($rawQueryString, $chartConfig->getDatabaseConnection());

            $configuration = [
                "time"=>$chartConfig->getExecutionLimit(),
                "rows"=>$chartConfig->getQueryLimit(),
                "chartType"=>$chartConfig->getTypeOfChart(),
                "offset"=>$chartConfig->getOffset(),
                "connection"=>$chartConfig->getDatabaseConnection(),
                "cronExpression"=>$queryRequest->getCronExpression()
            ];

            $results = $this->queryValidator->validateQueryExecution($rawQueryString, $configuration);

            $queryRequest->setQuery($query);
            $queryRequest->setConfig($chartConfig);

            $this->repository->save($queryRequest, $query, $chartConfig);

            return [
                "results" => $results,
                "query" => $queryRequest
            ];
        }
        catch (OffLimitsException $e)
        {
            //process default values
            $limit = $this->queryValidator->getMaxRows($chartConfig->getQueryLimit());
            $timeLimit = $this->queryValidator->getMaxTime($chartConfig->getExecutionLimit(), $chartConfig->getDatabaseConnection());
            $offset = 0;

            $chartConfig->setExecutionLimit($timeLimit);
            $chartConfig->setQueryLimit($limit);
            $chartConfig->setOffset($offset);

            $queryRequest->setQuery($query);
            $queryRequest->setConfig($chartConfig);

            $this->repository->save($queryRequest, $query, $chartConfig);

            $exception = new OffLimitsException("One of the limits were off limits, default ones were applied", $e->getCode(), $e);
            $exception->setData(["query" => $queryRequest, "results" => $results]);
            throw $exception;
        }
        catch (ParameterNotPassedException $e)
        {
            throw $e;
        }
        catch (TypeNotValidException $e)
        {
            throw $e;
        }
        catch (ValidationFailedException $e)
        {
            throw $e;
        }

    }

    /**
     * @param FormInterface $form
     * @param QChartsSubjectInterface $user
     * @param $queryId
     * @return array
     * @throws DatabaseException
     * @throws OffLimitsException
     * @throws ValidationFailedException
     * @throws OverlappingException
     */
    public function edit(FormInterface $form, QChartsSubjectInterface $user, $queryId)
    {
        /** @var QueryRequest $queryRequest */
        /** @var Form $form */
    	$queryRequest = $form->getData();
    	$query = $queryRequest->getQuery();
    	$config = $queryRequest->getConfig();

		try
        {
            $queryRequest->setModifiedLastBy($user);

            $rawQueryString = $query->getQuery();
            $query->setQueryHTML(\SqlFormatter::format($rawQueryString));

            $this->queryValidator->isValidQuery($rawQueryString, $config->getDatabaseConnection());

            $configuration = [
                "rows"=>$config->getQueryLimit(),
                "time"=>$config->getExecutionLimit(),
                "chartType"=>$config->getTypeOfChart(),
                "offset"=>$config->getOffset(),
                "connection"=>$config->getDatabaseConnection(),
                "cronExpression"=>$queryRequest->getCronExpression()
            ];

            $results = $this->queryValidator->validateQueryExecution($rawQueryString, $configuration);

            $queryRequest->setConfig($config);
            $queryRequest->setQuery($query);

            $this->repository->update($queryRequest);

            return [
                "query" => $queryRequest,
                "results" => $results
            ];
		}
        catch (OffLimitsException $e)
        {
            // set the default values
            $limit = $this->queryValidator->getMaxRows($config->getQueryLimit());
            $config->setQueryLimit($limit);
            $offset = 0;
            $config->setOffset($offset);
            $timeLimit = $this->queryValidator->getMaxTime($config->getExecutionLimit(), $config->getDatabaseConnection());
            $config->setExecutionLimit($timeLimit);

            $queryRequest->setConfig($config);
            $queryRequest->setQuery($query);

            $this->repository->update($queryRequest);

            throw new OffLimitsException("One of the limits were off limit, their respective default value were used", 200, $e);
        }
        catch (ValidationFailedException $e)
        {
			throw $e;
		}
    }

    /**
     * @param $queryId
     * @throws InstanceNotFoundException
     * @throws ParameterNotPassedException
     */
    public function delete($queryId)
    {
        /** @var QueryRequest $queryRequest */
    	$queryRequest = $this->getQueryRequestById($queryId);
    	$this->deleteQuery($queryRequest);
    }

    /**
     * @param QueryRequest $queryRequest
     * @throws ParameterNotPassedException
     */
    public function deleteQuery(QueryRequest $queryRequest)
    {
    	if (!$queryRequest)
        {
            throw new ParameterNotPassedException('The given Query was not valid', 500);
        }
    	$this->repository->deleteQuery($queryRequest);
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        return $this->dynamicRepo->getConnectionNames();
    }

    /**
     * @param string $connectionName
     * @return array
     */
    public function getSchemas($connectionName = "default")
    {
        $this->queryValidator->validateConnection($connectionName);
        $this->dynamicRepo->setUp($connectionName);

        return $this->dynamicRepo->getSchemaNames();
    }

    /**
     * @param string $connectionName
     * @param string $schemaName
     * @return array
     */
    public function getTableNames($schemaName, $connectionName = "default")
    {
        $this->queryValidator->validateConnection($connectionName);
        $this->dynamicRepo->setUp($connectionName);

        return $this->dynamicRepo->getTableNamesOfSchema($schemaName);
    }

    /**
     * @param ParameterBag $parameterBag
     * @return array
     * @throws InstanceNotFoundException
     * @throws ParameterNotPassedException
     */
    public function getTableInformation(ParameterBag $parameterBag)
    {
        $tableName = $parameterBag->get('tableName', '');
        $connectionName = $parameterBag->get('connection', 'default');

        if ($tableName == '')
        {
            throw new ParameterNotPassedException("Table Name was not passed", 404);
        }

        $this->queryValidator->validateConnection($connectionName);
        $this->dynamicRepo->setUp($connectionName);
        return $this->dynamicRepo->getColumnNamesFromTable($tableName);
    }

    /**
     * Calls the dynamic repository and calls the results from the query
     *
     * @param $query
     * @param int $limit
     * @param string $connection
     * @return array
     * @throws DatabaseException
     * @throws ParameterNotPassedException
     * @throws ValidationFailedException
     * @throws \Exception
     */
    public function getResultsFromQuery($query, $limit = 0, $connection='default')
    {
        try
        {
            $this->queryValidator->isValidQuery($query, $connection);

            $query = $this->queryValidator->getLimitedQuery($query, $limit);

            $this->dynamicRepo->setUp($connection);
            $res = $this->dynamicRepo->execute($query);

            $duration = $this->dynamicRepo->getExecutionDuration();
            $duration = number_format($duration, 7, '.', ',');

            return [
                "results" => $res,
                "duration" => $duration
            ];
        }
        catch (ParameterNotPassedException $e)
        {
            throw $e;
        }
        catch (ValidationFailedException $e)
        {
            throw $e;
        }
        catch (TypeNotValidException $e)
        {
            throw $e;
        }
        catch (DBALException $e)
        {
            $exceptionText = "There was an error when executing the query, your database's message:\n\n{$e->getPrevious()->getMessage()}";
            throw new DatabaseException($exceptionText, 500, $e);
        }
    }

}

