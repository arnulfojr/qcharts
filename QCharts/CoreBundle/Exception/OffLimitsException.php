<?php

namespace QCharts\CoreBundle\Exception;

class OffLimitsException extends QChartsBaseException
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