<?php

namespace QCharts\CoreBundle\Validation\Validator;

use QCharts\CoreBundle\Exception\OffLimitsException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

/**
 * Class TimeExecutionValidator
 * @package QCharts\CoreBundle\Validation\Validator
 */
class TimeExecutionValidator implements ValidatorInterface
{
    /** @var integer $duration */
    private $duration; // duration in seconds

    /** @var array $limits */
    private $limits;

    private $key = 'TimeExecution';

    /**
     *
     * Validates the duration with the limits given.
     * NOTE: Duration is recommended to be given in seconds!
     *
     * @return bool
     * @throws OffLimitsException
     * @throws ValidationFailedException
     */
    public function validate()
    {
        if (!is_null($this->duration) && $this->duration >= $this->limits[$this->key])
        {
            //throw new ValidationFailedException("The duration ({$this->duration}) of the requested query is greater than the time limit set ({$this->limits[$this->key]})", 500);
            throw new ValidationFailedException("The duration of the requested query is greater than the time limit set", 500);
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->duration;
    }

    /**
     * @param mixed $object
     * @throws TypeNotValidException
     */
    public function setObject($object)
    {
        if (!is_numeric($object))
        {
            throw new TypeNotValidException("Duration is expected to be numeric", 500);
        }
        $this->duration = $object;
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
        if (!is_string($key))
        {
            throw new TypeNotValidException('The type is not valid in ExistenceValidator#setKeyComparison, string is expected', 500);
        }
        $this->key = $key;
    }
}