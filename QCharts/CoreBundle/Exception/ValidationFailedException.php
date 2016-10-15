<?php

namespace QCharts\CoreBundle\Exception;

use \Exception;

class ValidationFailedException extends QChartsBaseException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}