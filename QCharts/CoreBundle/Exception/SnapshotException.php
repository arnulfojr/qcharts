<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/3/16
 * Time: 4:33 PM
 */

namespace QCharts\CoreBundle\Exception;


class SnapshotException extends NotFoundException
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}