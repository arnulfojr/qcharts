<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/19/16
 * Time: 3:44 PM
 */

namespace QCharts\CoreBundle\Repository;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Logging\DebugStack;
use \InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use QCharts\CoreBundle\Exception\DatabaseException;
use QCharts\CoreBundle\Exception\TypeNotValidException;

class DynamicRepository extends DynamicEntityManager
{
    /** @var Registry $doctrine */
    private $doctrine;
    /** @var DebugStack $logger */
    private $logger;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->logger = new DebugStack();
        $this->logger->enabled = false;
    }

    /**
     * @param string $connectionName
     * @throws DatabaseException
     */
    public function setUp($connectionName = 'default')
    {
        try
        {
            /** @var EntityManagerInterface $em */
            $em = $this->doctrine->getManager($connectionName);
            $this->setEntityManager($em);
        }
        catch (InvalidArgumentException $e)
        {
            $className = get_class($e);
            $text = "{$e->getMessage()}, {$className},Check that the connection is set up correctly in the project's configuration";
            $text = $text." Check that the orm.entity_managers defines also the connection: {$connectionName}";
            throw new DatabaseException($text, 500, $e);
        }
    }

    /**
     * @param $query
     * @return array
     * @throws DatabaseException
     */
    public function execute($query)
    {
        try
        {
            $connection = $this->getConnection();
            $connection->getConfiguration()->setSQLLogger($this->logger);
            $this->logger->enabled = true;
            $stmt = $connection->query($query);

            /** @var array $results */
            $results = $stmt->fetchAll();

            $this->logger->enabled = false;

            return $results;
        }
        catch (DBALException $e)
        {
            $exceptionText = "There was an error when executing the query, your database's message:\n\n{$e->getMessage()}";
            throw new DatabaseException($exceptionText, 500, $e);
        }
    }

    /**
     * @return array
     */
    public function getConnectionNames()
    {
        $connections = $this->doctrine->getConnectionNames();
        return $connections;
    }

    /**
     * @return array
     */
    public function getCanonicalConnectionNames()
    {
        $connections = $this->getConnectionNames();
        $toReturn = [];
        foreach ($connections as $key=>$value)
        {
            $toReturn[$key] = $key;
        }
        return $toReturn;
    }

    /**
     * @return float
     * @throws DatabaseException
     */
    public function getExecutionDuration()
    {
        $totalTime = 0.0;

        if (is_null($this->logger))
        {
            throw new DatabaseException("Logger is not initialized", 500);
        }

        foreach ($this->logger->queries as $query)
        {
            $totalTime = $totalTime + $query["executionMS"];
        }

        // return the seconds
        return $totalTime/1000;
    }

    /**
     * @return bool|number
     */
    public function isMaxExecutionSet()
    {
        $results =  $this->execute(DatabaseQueries::SHOW_SESSION_EXECUTION);

        if (!is_null($results) && count($results) <= 0)
        {
            return false;
        }

        foreach ($results as $row)
        {
            if ($row["Value"] == 0)
            {
                return false;
            }
            return $row["Value"];
        }

        return false;
    }

    /**
     * Returns the names of the schemas of the current connection
     * @return array
     */
    public function getSchemaNames()
    {
        return $this->execute(DatabaseQueries::SHOW_DATABASES);
    }

    /**
     * Returns the names of the tables from the specified schema, uses the current connection
     * @param $schemaName
     * @return array
     */
    public function getTableNamesOfSchema($schemaName)
    {
        return $this->execute(DatabaseQueries::getSQLForTableNames($schemaName));
    }

    /**
     * Returns all the name of the tables from the connection
     * @return array
     * @throws DatabaseException
     */
    public function getAllTableNames()
    {
        return $this->execute(DatabaseQueries::getSQLForAllTableNames());
    }

    /**
     * Returns the information of the colummns in the given table name
     * @param $tableName
     * @return array
     */
    public function getColumnNamesFromTable($tableName)
    {
        return $this->execute(DatabaseQueries::getSQLForColumnNames($tableName));
    }

}