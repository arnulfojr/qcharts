<?php

namespace QCharts\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Logging\DebugStack;
use \InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use QCharts\CoreBundle\Exception\DatabaseException;


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
     * @param $query
     * @return int
     * @throws DatabaseException
     */
    protected function set($query)
    {
        try
        {
            $connection = $this->getConnection();
            $statement = $connection->prepare($query);
            return $statement->execute();
        }
        catch (DBALException $e)
        {
            $exceptionText = "There was an error when setting the max_%%_time SESSION VARIABLE, your database's message:\n\n{$e->getMessage()}";
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
     * Returns FALSE if the Connection returned no variable regarding to the time limit
     * Returns NUMBER if the Connection encountered a variable regarding to the time limit
     *  in seconds
     * Returns STRING if the Connection encountered a variable regarding to the time limit but
     *  the value is 0 (hence not set)
     *
     * http://dev.mysql.com/doc/refman/5.7/en/server-system-variables.html#sysvar_max_execution_time
     *
     * @return bool|number|string
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
                // return the name of the variable evaluated
                return $row["Variable_name"];
            }

            $millisecondsLimit = $row["Value"];
            return $millisecondsLimit / 1000; // return only seconds
        }

        return false;
    }

    /**
     * @param int $duration
     * @param string $variableName
     * @return bool
     * @throws DatabaseException
     */
    public function setMaxExecutionTime($duration, $variableName)
    {
        $millisecondsLimit = 1000 * $duration; // $duration is in seconds -> MySQL in milliseconds
        $millisecondsLimit = intval($millisecondsLimit);
        $query = DatabaseQueries::getSessionVariableUpdate($variableName, $millisecondsLimit);
        return $this->set($query);
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