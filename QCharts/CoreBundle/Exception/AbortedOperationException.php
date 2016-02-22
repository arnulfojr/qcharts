<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/4/16
 * Time: 11:15 AM
 */

namespace QCharts\CoreBundle\Exception;


class AbortedOperationException extends \Exception
{
    public function __construct($m, $c = 0, $p = null)
    {
        parent::__construct($m, $c, $p);
    }
}