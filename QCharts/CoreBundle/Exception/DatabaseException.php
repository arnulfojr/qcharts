<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/7/16
 * Time: 10:45 AM
 */

namespace QCharts\CoreBundle\Exception;

use \Exception;

class DatabaseException extends Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}