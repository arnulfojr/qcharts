<?php
/**
 * Created by PhpStorm.
 * User: arnulfosolis
 * Date: 1/21/16
 * Time: 22:36
 */

namespace QCharts\CoreBundle\ResultFormatter;


class OneDimensionTableFormatter implements ResultFormatterInterface
{

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function formatResults(array $rawResults)
    {
        $results = [];
        foreach ($rawResults as $value)
        {
            $results[] = "{$value['TABLE_SCHEMA']}.{$value['TABLE_NAME']}";
        }

        return $results;
    }

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function getSeries(array $rawResults)
    {
        return $rawResults;
    }

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function getYAxis(array $rawResults)
    {
        return $rawResults;
    }
}