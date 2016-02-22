<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/27/16
 * Time: 12:27 PM
 */

namespace QCharts\CoreBundle\Exception;


class OverlappingException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}