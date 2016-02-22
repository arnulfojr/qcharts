<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/11/16
 * Time: 11:02 AM
 */

namespace QCharts\CoreBundle\Exception;

use \Exception;

class OffLimitsException extends Exception
{
    /** @var array $data */
    private $data;

    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

}