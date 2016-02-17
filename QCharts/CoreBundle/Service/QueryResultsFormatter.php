<?php

namespace QCharts\CoreBundle\Service;

use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\ResultFormatter\ResultsFormatterFactory;
use QCharts\CoreBundle\ResultFormatter\ResultsFormatterFactoryInterface;
use QCharts\CoreBundle\Service\ServiceInterface\ChartValidatorInterface;

class QueryResultsFormatter
{
    /** @var ResultsFormatterFactory */
    private $formatterFactory;
    /** @var array $limits */
    private $limits;

    public function __construct(ResultsFormatterFactoryInterface $formatterFactory, array $limits)
    {
        $this->formatterFactory = $formatterFactory;
        $this->limits = $limits;
    }

    /**
     * @param array $rawResults
     * @param $type
     * @return array
     * @throws TypeNotValidException
     */
    public function formatResults(array $rawResults, $type)
    {
        try
        {
            $this->formatterFactory->setFormatType($type);
            $formatter = $this->formatterFactory->getFormatter();

            return $formatter->formatResults($rawResults);
        }
        catch (TypeNotValidException $e)
        {
            throw $e;
        }
    }

}