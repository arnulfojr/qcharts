<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 4:07 PM
 */

namespace QCharts\CoreBundle\Service;


class LimitsService
{
    /**
     * @var array $limits
     */
    private $limits;

    /**
     * LimitsService constructor.
     * @param array $limits
     */
    public function __construct(array $limits)
    {
        $this->limits = $limits;
    }

    /**
     * @param array $limits
     */
    public function setLimits($limits)
    {
        $this->limits = $limits;
    }

    /**
     * @param string $key
     * @param mixed $object
     */
    public function addLimit($key, $object)
    {
        $this->limits[$key] = $object;
    }

    /**
     * @return array
     */
    public function getLimits()
    {
        return $this->limits;
    }
}