<?php


namespace QCharts\CoreBundle\Exception;

/**
 * Class NotPlotableException
 * @package QCharts\CoreBundle\Exception
 */
class NotPlotableException extends QChartsBaseException
{
    /**
     * NotPlotableException constructor.
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}