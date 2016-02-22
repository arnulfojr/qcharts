<?php

namespace QCharts\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChartConfig
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ChartConfig
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="typeOfChart", type="string", length=255)
     */
    private $typeOfChart;

    /**
     * @var integer
     * @ORM\Column(name="queryLimit", type="integer")
     */
    private $queryLimit;

    /**
     * @var float
     * @ORM\Column(name="executionLimit", type="float")
     */
    private $executionLimit;

    /**
     * @var integer
     * @ORM\Column(name="offset", type="integer")
     */
    private $offset;

    /**
     * @var string
     * @ORM\Column(name="connection", type="string", length=255)
     */
    private $databaseConnection;

    /**
     * @var integer
     * @ORM\Column(name="cached", type="smallint")
     */
    private $isCached;

    /**
     * @var \DateTime
     * @ORM\Column(name="fetched_on", type="datetimetz", nullable=true)
     */
    private $fetchedOn;


    /**
     * ChartConfig constructor.
     */
    public function __construct()
    {
        $this->fetchedOn = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set typeOfChart
     *
     * @param string $typeOfChart
     *
     * @return ChartConfig
     */
    public function setTypeOfChart($typeOfChart)
    {
        $this->typeOfChart = $typeOfChart;

        return $this;
    }

    /**
     * Get typeOfChart
     *
     * @return string
     */
    public function getTypeOfChart()
    {
        return $this->typeOfChart;
    }

    /**
     * Set queryLimit
     *
     * @param integer $queryLimit
     *
     * @return ChartConfig
     */
    public function setQueryLimit($queryLimit)
    {
        $this->queryLimit = $queryLimit;

        return $this;
    }

    /**
     * Get queryLimit
     *
     * @return integer
     */
    public function getQueryLimit()
    {
        return $this->queryLimit;
    }


    /**
     * Set executionLimit
     *
     * @param float $executionLimit
     *
     * @return ChartConfig
     */
    public function setExecutionLimit($executionLimit)
    {
        $this->executionLimit = $executionLimit;

        return $this;
    }

    /**
     * Get executionLimit
     *
     * @return float
     */
    public function getExecutionLimit()
    {
        return $this->executionLimit;
    }

    /**
     * Set offset
     *
     * @param integer $offset
     *
     * @return ChartConfig
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set databaseConnection
     *
     * @param string $databaseConnection
     *
     * @return ChartConfig
     */
    public function setDatabaseConnection($databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;

        return $this;
    }

    /**
     * Get databaseConnection
     *
     * @return string
     */
    public function getDatabaseConnection()
    {
        return $this->databaseConnection;
    }

    /**
     * Set isCached
     *
     * @param integer $isCached
     *
     * @return ChartConfig
     */
    public function setIsCached($isCached = false)
    {
        $this->isCached = $isCached;

        return $this;
    }

    /**
     * Get isCached
     *
     * @return integer
     */
    public function getIsCached()
    {
        return $this->isCached;
    }

    /**
     * Set fetchedOn
     *
     * @param \DateTime $fetchedOn
     *
     * @return ChartConfig
     */
    public function setFetchedOn($fetchedOn = null)
    {
        if (!is_null($fetchedOn))
        {
            $this->fetchedOn = $fetchedOn;
            return $this;
        }
        $this->fetchedOn = new \DateTime('now');
        return $this;
    }

    /**
     * Get fetchedOn
     *
     * @return \DateTime
     */
    public function getFetchedOn()
    {
        return $this->fetchedOn;
    }
}
