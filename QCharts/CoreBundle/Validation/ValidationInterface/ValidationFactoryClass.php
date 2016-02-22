<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 10:03 AM
 */

namespace QCharts\CoreBundle\Validation\ValidationInterface;


abstract class ValidationFactoryClass
{

    /** @var  array $validators */
    protected $validators;
    /** @var  array $limits */
    protected $limits;
    /** @var string $query */
    protected $queryString;

    /**
     * @param array $limits
     */
    public function setLimits(array $limits)
    {
        $this->limits = $limits;
    }

    /**
     * @param $limit
     */
    public function addLimit($limit)
    {
        $this->limits[] = $limit;
    }

    /**
     * @return array
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * @param string $key
     * @param ValidatorInterface $validatorInterface
     */
    public function addValidator($key, ValidatorInterface $validatorInterface)
    {
        //add a validator on the fly
        $this->validators[$key] = $validatorInterface;
    }

    /**
     * @param array $validators
     */
    public function setValidators(array $validators)
    {
        $this->validators = $validators;
    }

    abstract public function registerValidators();

    abstract public function getValidators();

    abstract protected function addLimitsToValidators();

    /**
     * @param mixed $descriptor
     * @return mixed
     */
    public function getValidator($descriptor)
    {
        return $this->validators[$descriptor];
    }

}