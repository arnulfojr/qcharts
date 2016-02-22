<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/13/16
 * Time: 12:23 PM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class NumericValueValidator implements ValidatorInterface
{
    /** @var mixed $value */
    private $value;
    /** @var array $limits */
    private $limits;
    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        if (is_numeric($this->value))
        {
            return true;
        }
        throw new ValidationFailedException("The value is not numeric", 500);
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->value;
    }

    /**
     * @param mixed $object
     * @throws TypeNotValidException
     */
    public function setObject($object)
    {
        $this->value = $object;
    }

    /**
     * @return array
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * @param array $limits
     */
    public function setLimits(array $limits)
    {
        $this->limits = $limits;
    }

    /**
     * Sets the key used to call the value from the limits array
     * @param $key
     * @throws TypeNotValidException
     */
    public function setKeyComparison($key)
    {
        return;
    }
}