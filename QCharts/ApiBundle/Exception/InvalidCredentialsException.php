<?php


namespace QCharts\ApiBundle\Exception;

class InvalidCredentialsException extends \Exception
{
    /**
     * InvalidCredentialsException constructor.
     * @param string $m
     * @param int $c
     * @param null $p
     */
    public function __construct($m, $c = 0, $p = null)
    {
        parent::__construct($m, $c, $p);
    }
}