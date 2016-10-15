<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 12/29/15
 * Time: 2:11 PM
 */

namespace QCharts\CoreBundle\ResultFormatter;


class ResultsPrepareFormatter
{
    /**
     * @param array $results
     * @return array
     */
    public function formatResults(array $results)
    {
        return $this->prepareResults($results);
    }

    /**
     * @param array $results
     * @return array
     */
    public function prepareResults(array $results)
    {
        if (count($results) === 0)
        {
            return [];
        }

        $headers = $this->getEmptyDictionary($this->getHeadersFromList($results[0]));
        foreach ($results as $row)
        {
            foreach($row as $key=>$value)
            {
                $headers[$key][] = ($value !== null) ? $value : 0;
            }
        }
        return $headers;
    }

    /**
     * @param array $row
     * @return array
     */
    public function getHeadersFromList(array $row)
    {
        $toReturn = [];
        foreach ($row as $key=>$value)
        {
            $toReturn[] = $key;
        }
        return $toReturn;
    }

    /**
     * @param $headers
     * @return array
     */
    public function getEmptyDictionary($headers)
    {
        $toReturn = [];
        foreach ($headers as $header)
        {
            $toReturn[$header] = [];
        }
        return $toReturn;
    }
}