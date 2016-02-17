<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/18/16
 * Time: 10:24 AM
 */

namespace QCharts\CoreBundle\Exception;

use \Exception;

class EmptyCallException extends Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}