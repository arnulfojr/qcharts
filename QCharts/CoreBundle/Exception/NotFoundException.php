<?php

namespace QCharts\CoreBundle\Exception;


class NotFoundException extends QChartsBaseException
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}