<?php

namespace QCharts\CoreBundle\Exception;

use \Exception;

class WriteReadException extends Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}