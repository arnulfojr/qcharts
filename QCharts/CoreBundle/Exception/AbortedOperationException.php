<?php

namespace QCharts\CoreBundle\Exception;


class AbortedOperationException extends QChartsBaseException
{
    public function __construct($m, $c = 0, $p = null)
    {
        parent::__construct($m, $c, $p);
    }
}