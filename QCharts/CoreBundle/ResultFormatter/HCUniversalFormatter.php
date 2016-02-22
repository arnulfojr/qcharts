<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 12/29/15
 * Time: 10:42 AM
 */

namespace QCharts\CoreBundle\ResultFormatter;


use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Service\ServiceInterface\ChartValidatorInterface;

class HCUniversalFormatter extends ResultsPrepareFormatter implements ResultFormatterInterface
{

    /** @var  ChartValidatorInterface */
    private $chartValidator;

    /** @var string $chartType */
    protected $chartType;
    /**
     * @param ChartValidatorInterface $cv
     * @param string $type
     */
    public function __construct(ChartValidatorInterface $cv, $type = 'line')
    {
        $this->chartValidator = $cv;
        $this->chartType = $type;
    }

    /**
     * @param array $rawResults
     * @return array
     * @throws ValidationFailedException
     */
    public function formatResults(array $rawResults)
    {
        $rawResults = $this->prepareResults($rawResults);
        $processedList = [];
        $processedList["chart"] = $this->getChartConfig();
        $processedList["xAxis"] = $this->getXAxis($rawResults);
        $processedList["series"] = $this->getSeries($rawResults);
        $processedList["yAxis"] = $this->getYAxis($rawResults);

        return $processedList;
    }

    /**
     * @return array
     */
    public function getChartConfig()
    {
        return [
            "type"=>$this->chartType,
            "zoomType"=>"x"
        ];
    }

    /**
     * @param array $rawResults
     * @return array
     */
    public function getSeries(array $rawResults)
    {
        $rawResults = array_slice($rawResults, 1 , count($rawResults));

        $results = [];
        $index = 0;

        foreach($rawResults as $key=>$values)
        {
            $temp = [
                "name"=>$key,
                "yAxis"=>$index,
                "data"=>$values
            ];

            $results[] = $temp;
            $index = $index + 1;
        }

        return $results;
    }

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function getYAxis(array $rawResults)
    {
        $rawResults = array_slice($rawResults, 1, count($rawResults));
        $results = [];
        $index = 0;

        foreach($rawResults as $key=>$value)
        {
            $temp = [
                "labels" => [
                    "format" => "{value}",
                ],
                "title"=>[
                    "text"=>$key,
                ]
            ];

            $results[] = $temp;
            $index = $index + 1;
        }

        return $results;
    }

    /**
     * @param array $rawResults
     * @return mixed
     * @throws ValidationFailedException
     */
    protected function getXAxis(array $rawResults)
    {
        foreach($rawResults as $key=>$value)
        {
            $result["categories"] = $value;
            return $result;
        }
        throw new ValidationFailedException("Empty results can not be formatted", 500);
    }
}