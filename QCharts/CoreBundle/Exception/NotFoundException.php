<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/26/16
 * Time: 2:57 PM
 */

namespace QCharts\CoreBundle\Exception;


class NotFoundException extends \Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}