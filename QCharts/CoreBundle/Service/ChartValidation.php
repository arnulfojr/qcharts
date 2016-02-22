<?php


namespace QCharts\CoreBundle\Service;


use QCharts\CoreBundle\Exception\NotPlotableException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Service\ServiceInterface\ChartValidatorInterface;
use QCharts\CoreBundle\Validation\Validator\ChartTypeValidator;
use QCharts\CoreBundle\Validation\Validator\ExistenceValidator;
use QCharts\CoreBundle\Validation\Validator\NumericListValidator;
use QCharts\CoreBundle\Validation\Validator\PieCompabilityValidator;

class ChartValidation implements ChartValidatorInterface
{

    private $chartOptions;

    /**
     * @param array $chartOptions
     */
    public function __construct(array $chartOptions)
    {
        $this->chartOptions = $chartOptions;
    }


    /**
     * @param $chartType
     * @return bool
     * @throws TypeNotValidException
     */
    public function chartTypeIsValid($chartType)
    {
        $validator = new ExistenceValidator();
        $validator->setLimits(["Existence" => $this->chartOptions]);
        $validator->setObject($chartType);
        return $validator->validate();
    }

    /**
     * @param array $rawArray
     * @return bool
     * @throws ValidationFailedException
     */
    public function resultsArePieCompatible(array $rawArray)
    {
        $columnValidator = new PieCompabilityValidator();
        $columnValidator->setObject(["results"=>$rawArray, "chartType"=>"pie"]);
        return $columnValidator->validate();
    }

    /**
     * @param array $array
     * @param string $chartType
     * @return bool
     * @throws TypeNotValidException
     * @throws ValidationFailedException
     */
    public function resultsAreNumeric(array $array, $chartType)
    {
        $numericValidator = new NumericListValidator();
        $numericValidator->setObject($array);
        $numericValidator->setLimits(["NumericList" => $chartType]);
        return $numericValidator->validate();
    }

    /**
     * @return array
     */
    public function getChartTypes()
    {
        return $this->chartOptions;
    }
}