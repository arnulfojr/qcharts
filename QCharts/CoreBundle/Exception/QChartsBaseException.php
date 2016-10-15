<?php


namespace QCharts\CoreBundle\Exception;

use \Exception;

class QChartsBaseException extends Exception
{
    /**
     * Base Exception for all QCharts Related exceptions
     * QChartsBaseException constructor.
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}