<?php
/**
 * Created by PhpStorm.
 * User: arnulfosolis
 * Date: 1/30/16
 * Time: 10:47
 */

namespace QCharts\CoreBundle\Exception;


class DirectoryNotEmptyException extends \Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}