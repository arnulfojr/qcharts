<?php

namespace QCharts\CoreBundle\ResultFormatter;


use QCharts\CoreBundle\Exception\ParameterNotPassedException;
use QCharts\CoreBundle\Service\ServiceInterface\ChartValidatorInterface;
use QCharts\CoreBundle\Validation\Validator\NumericValueValidator;

class HCPieFormatter extends ResultsPrepareFormatter implements ResultFormatterInterface
{

    /** @var ChartValidatorInterface */
    private $chartValidator;

    /**
     * @param ChartValidatorInterface $cv
     */
    public function __construct(ChartValidatorInterface $cv)
    {
        $this->chartValidator = $cv;
    }

    /**
     * @param array $rawResults
     * @return array
     */
    public function formatResults(array $rawResults)
    {
        $rawResults = $this->prepareResults($rawResults);
        $this->chartValidator->resultsArePieCompatible($rawResults);

        $processedResults = [];
        $processedResults["chart"] = $this->getChartConfig();
        $processedResults["series"] = $this->getSeries($rawResults);
        $processedResults["yAxis"] = $this->getYAxis($rawResults);

        return $processedResults;
    }

    public function getChartConfig()
    {
        return [
            "zoomType"=>'x',
            "type"=>'pie'
        ];
    }

    /**
     * @param array $rawResults
     * @return mixed
     */
    public function getSeries(array $rawResults)
    {
        $results = [];

        $results["name"] = $this->getSeriesNames($rawResults);
        $yValueNames = array_keys($rawResults);
        $yValues = array_reverse(array_pop($rawResults));
        $nameValues = array_pop($rawResults);

        $title = current($yValueNames);

        $numericValidator = new NumericValueValidator();

        foreach ($nameValues as $key=>$name)
        {
            $temp = [
                "name" => "{$name} {$title}",
                "y" => array_pop($yValues)
            ];
            $numericValidator->setObject($temp["y"]);
            $numericValidator->validate();

            $results["data"][] = $temp;
        }

        return $results;
    }

    /**
     * @param array $rawResults
     * @return mixed
     * @throws ParameterNotPassedException
     */
    protected function getSeriesNames(array $rawResults)
    {
        if (!is_null($rawResults))
        {
            $keys = array_keys($rawResults);
            $key = array_pop($keys);
            return $key;
        }
        throw new ParameterNotPassedException("The passed results to format are not set", 500);
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

        foreach ($rawResults as $key => $value)
        {
            $temp = [
                "labels" => [
                    "format" => "{value}",
                    //"style"=>["color"=>$index]
                ],
                [
                    "title" => [
                        "text" => $key,
                        //"style"=>["color"=>$index]
                    ]
                ]
            ];

            $results[] = $temp;
            $index = $index + 1;
        }

        return $results;


    }
}