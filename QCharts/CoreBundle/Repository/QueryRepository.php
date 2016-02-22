<?php

namespace QCharts\CoreBundle\Repository;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException as ORMQueryException;
use Doctrine\DBAL\Query\QueryException as DBALQueryException;
use QCharts\CoreBundle\Entity\User\QChartsSubjectInterface;
use QCharts\CoreBundle\Exception\DatabaseException;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\NoTableNamesException;
use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\OverlappingException;
use QCharts\CoreBundle\Exception\ParameterNotPassedException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Entity\Query;
use QCharts\CoreBundle\Entity\ChartConfig;
use QCharts\CoreBundle\Exception\SQLException;
use Doctrine\DBAL\Logging\DebugStack;
use QCharts\CoreBundle\Exception\TypeNotValidException;

class QueryRepository extends EntityRepository
{

	private $logger;

    protected function startLogging()
    {
        if (is_null($this->logger))
        {
            $this->getLogger();
        }
        $this->logger->enabled = true;
    }

    protected function stopLogging()
    {
        $this->logger->enabled = false;
    }

    /**
     * @return DebugStack
     */
	public function getLogger()
	{
		$this->logger = (is_null($this->logger)) ? new DebugStack() : $this->logger;

		$this
			->getEntityManager()
			->getConnection()
			->getConfiguration()
			->setSQLLogger($this->logger);
		return $this->logger;
	}

    public function resetLogger()
	{
        $this->stopLogging();
		$this->logger->queries = [];
	}

    /**
     * @return float
     */
	public function getDurationOfLastQuery()
	{
		$totalTime = 0.0;
		foreach ($this->logger->queries as $query)
		{
			$totalTime = $totalTime + $query["executionMS"];
		}

		return $totalTime/1000;
	}

    /**
     * @return mixed
     */
    public function getQueriesLogged()
    {
        return $this->logger->queries;
    }

    /**
     * @return array
     * @throws NoTableNamesException
     */
	public function findAllOrderedByDateCreated()
	{
		//testing the repository
		$query = $this->createQueryBuilder('q')
			->orderBy('q.dateCreated', 'desc')
			->getQuery();
		$results = $query->getResult();
        if (is_null($results))
        {
            throw new NoTableNamesException("There was no results in the requested query", 500);
        }
        return $results;
	}

    /**
     * @param null $directoryId
     * @return array
     * @throws NotFoundException
     */
    public function getQueriesInDirectory($directoryId = null)
    {
        //if directoryId is null the fetch the queries in the master-root
        $query = $this->getQueryByDirectory($directoryId);
        return $query->getResult();
    }

    /**
     * @param null $directoryId
     * @return \Doctrine\ORM\Query
     * @throws TypeNotValidException
     */
    protected function getQueryByDirectory($directoryId = null)
    {
        if ($directoryId)
        {
            if (is_numeric($directoryId))
            {
                //query for it
                $queryBuilder = $this->createQueryBuilder('q');
                $queryBuilder->join("q.directory", "d")
                    ->where("d.id = :dirId")
                    ->setParameter('dirId', $directoryId);
                return $queryBuilder->getQuery();
            }
            throw new TypeNotValidException(ExceptionMessage::TYPE_NOT_VALID("folder id should be numeric"), 500);
        }
        return $this->createQueryBuilder('d')->where('d.directory is null')->getQuery();
    }

    /**
     * @param $queryRequestId
     * @return string
     * @throws InstanceNotFoundException
     * @throws \Exception
     */
	public function getQueryWithQueryRequestId($queryRequestId)
    {
        /** @var QueryRequest $queryRequest */
		$queryRequest = $this->find($queryRequestId);

		if (!$queryRequest)
        {
            throw new InstanceNotFoundException("Query was not found", 404);
        }
		return $this->getQueryFromQueryRequest($queryRequest);
	}

    /**
     * @param QueryRequest $qr
     * @return int
     * @throws ParameterNotPassedException
     */
    public function getRowLimitFromQueryRequest(QueryRequest $qr)
    {
        if (is_null($qr))
        {
            throw new ParameterNotPassedException("No query was passed to fetch the limit of rows requested", 404);
        }
        return $qr->getConfig()->getQueryLimit();
    }

    /**
     * @param QueryRequest $qr
     * @return string
     * @throws ParameterNotPassedException
     */
	public function getQueryFromQueryRequest(QueryRequest $qr)
    {
		//only shortcut
		if (!$qr)
        {
            throw new ParameterNotPassedException('No Query Request to fetch the Query from');
        }
		return $qr->getQuery()->getQuery();
	}

    /**
     * @param QueryRequest $qr
     * @param Query $q
     * @param ChartConfig $config
     */
	public function save(QueryRequest $qr, Query $q, ChartConfig $config)
    {
		$this->getEntityManager()->persist($qr);
		$this->getEntityManager()->persist($q);
		$this->getEntityManager()->persist($config);
		$this->getEntityManager()->flush();
	}

    /**
     * @param QueryRequest $queryRequest
     * @param bool $autoFlush
     * @throws DatabaseException
     * @throws OverlappingException
     */
    public function update(QueryRequest $queryRequest, $autoFlush = true)
    {
        try
        {
            $this->getEntityManager()->persist($queryRequest);
            if ($autoFlush)
            {
                $this->getEntityManager()->flush();
            }
        }
        catch (UniqueConstraintViolationException $e)
        {
            throw new OverlappingException("Element already exists in the given context", $e->getCode(), $e);
        }
        catch (DriverException $e)
        {
            throw new DatabaseException("Error in the database", $e->getCode(), $e);
        }
    }

    /**
     * @param QueryRequest $qr
     * @param Query $q
     * @param ChartConfig $config
     */
    public function delete(QueryRequest $qr, Query $q, ChartConfig $config)
    {
		$this->getEntityManager()->remove($config);
		$this->getEntityManager()->remove($q);
		$this->getEntityManager()->remove($qr);
		$this->getEntityManager()->flush();
	}

    /**
     * @param QueryRequest $qr
     */
	public function deleteQuery(QueryRequest $qr)
    {
		$this->delete($qr, $qr->getQuery(), $qr->getConfig());
	}

    /**
     * @return array
     */
    public function getCachedQueries()
    {
        // return only the queries that are cached!
        $queryBuilder = $this->createQueryBuilder("q");
        $queryBuilder->join("q.config", "c")->where("c.isCached > 0");
        $query = $queryBuilder->getQuery();
        $results = $query->getResult();
        return $results;
    }

    /**
     * @return array
     */
    public function getTimeMachineQueries()
    {
        $queryBuilder = $this->createQueryBuilder("q");
        $queryBuilder->join("q.config", "c")->where("c.isCached = 2");
        $query = $queryBuilder->getQuery();
        $results = $query->getResult();
        return $results;
    }

    /**
     * @param QueryRequest $queryRequest
     * @param \DateTime $dateTime
     * @param bool $autoFlush
     */
    public function setUpdatedOn(QueryRequest $queryRequest, \DateTime $dateTime, $autoFlush = true)
    {
        $queryRequest->getConfig()->setFetchedOn($dateTime);
        $this->getEntityManager()->persist($queryRequest);
        ($autoFlush) ? $this->getEntityManager()->flush() : null;
    }

    /**
     * @param QChartsSubjectInterface $user
     * @return array
     * @throws DatabaseException
     */
    public function getFavouritesBy(QChartsSubjectInterface $user)
    {
        try
        {
            //return a list of query request!
            $queryBuilder = $this->createQueryBuilder('qr');
            $queryBuilder
                ->select("qr.title")
                ->addSelect("qr.id")
                ->addSelect("qr.dateLastModified")
                ->addSelect("qr.dateCreated")
                ->innerJoin('qr.favoritedBy', 'fav')
                ->where("fav = :userID")
                ->setParameter("userID", $user->getId())
            ;

            $query = $queryBuilder->getQuery();
            $results = $query->getResult();

            return $results;
        }
        catch (ORMQueryException $e)
        {
            throw new DatabaseException("Error while trying to fetch favorites", $e->getCode(), $e);
        }
        catch (DBALQueryException $e)
        {
            throw new DatabaseException("Error while trying to fetch favorites", $e->getCode(), $e);
        }
    }

}