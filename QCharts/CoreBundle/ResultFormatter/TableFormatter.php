<?php


namespace QCharts\CoreBundle\ResultFormatter;

use QCharts\CoreBundle\Exception\EmptyCallException;

class TableFormatter implements ResultFormatterInterface
{

    /** @var int */
    private $max_row_limit;

    /**
     * @param $limit
     */
    public function __construct($limit = 0)
    {
        $this->max_row_limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        if ($this->max_row_limit == 0)
        {
            $this->setLimit();
        }
        return $this->max_row_limit;
    }

    /**
     * @param $limit
     */
    public function setLimit($limit = 0)
    {
        $this->max_row_limit = $limit;
    }

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function formatResults(array $rawResults)
    {
        if (count($rawResults) > $this->getLimit())
        {
            $rawResults = array_slice($rawResults, 0, $this->getLimit());
        }
        return $rawResults;
    }

    /**
     * @param array $rawResults
     * @return mixed|void
     * @throws EmptyCallException
     */
    public function getSeries(array $rawResults)
    {
        throw new EmptyCallException("Table Formatting does not support Series formatting", 500);
    }

    /**
     * @param array $rawResults
     * @return mixed|void
     * @throws EmptyCallException
     */
    public function getYAxis(array $rawResults)
    {
        throw new EmptyCallException("Table Formatting does not support Y Axis Formatting", 500);
    }
}