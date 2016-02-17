<?php

namespace QCharts\CoreBundle\ResultFormatter;


use QCharts\CoreBundle\Exception\ParameterNotPassedException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Service\ServiceInterface\ChartValidatorInterface;

class ResultsFormatterFactory implements ResultsFormatterFactoryInterface
{
    /** @var string $chartType */
    private $chartType;
    /** @var ChartValidatorInterface */
    private $chartValidator;
    /** @var array $options */
    private $options;

    /**
     * @param ChartValidatorInterface $chartValidator
     * @param array|null $options
     */
    public function __construct(ChartValidatorInterface $chartValidator, array $options = null)
    {
        $this->chartValidator = $chartValidator;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getFormatType()
    {
        return $this->chartType;
    }

    /**
     * @param $formatType
     */
    public function setFormatType($formatType)
    {
        if ($this->chartValidator->chartTypeIsValid($formatType))
        {
            $this->chartType = $formatType;
        }
    }

    /**
     * @return HCPieFormatter|HCUniversalFormatter
     * @throws TypeNotValidException
     */
    public function getFormatter()
    {
        if (!is_null($this->chartType))
        {
            return $this->decideFormatter($this->chartType);
        }

        throw new TypeNotValidException("The chart type was not passed correctly", 500);
    }

    /**
     * @param $type
     * @return HCPieFormatter|HCUniversalFormatter|TableFormatter
     * @throws TypeNotValidException
     */
    protected function decideFormatter($type)
    {
        switch ($type)
        {
            case 'pie':
                return new HCPieFormatter($this->chartValidator);
                break;
            case 'table':
                if (array_key_exists('row', $this->options))
                {
                    return new TableFormatter($this->options["row"]);
                }
                throw new TypeNotValidException("When deciding the type, the row limit field was not defined", 500);
                break;
            case stristr($type, "polar_"):
                return new HCPolarFormatter($this->chartValidator, $type);
                break;
            case 'prepare':
                return new ResultsPrepareFormatter();
                break;
            default:
                return new HCUniversalFormatter($this->chartValidator, $type);
        }
    }

}