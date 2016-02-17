<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/7/16
 * Time: 11:26 AM
 */

namespace QCharts\CoreBundle\Exception;

use \Exception;

class TypeNotValidException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}