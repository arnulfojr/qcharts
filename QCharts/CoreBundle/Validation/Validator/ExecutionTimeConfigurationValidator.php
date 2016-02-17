<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/11/16
 * Time: 12:22 PM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\OffLimitsException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class ExecutionTimeConfigurationValidator implements ValidatorInterface
{
    private $duration;
    private $limits;
    private $key = 'ExecutionTimeConfiguration';
    /**
     * @return bool
     * @throws OffLimitsException
     */
    public function validate()
    {
        if ($this->duration == 0 || $this->duration > $this->limits[$this->key])
        {
            throw new OffLimitsException("The assigned duration is off limits, duration: {$this->duration}", 500);
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
        if (is_null($object))
        {
            $object = 0;
        }
        if (!is_numeric($object))
        {
            $className = get_class($this);
            $isNull = is_null($object);
            $description = "Type is not valid (numeric) in {$className} with object: {$object}, is_null: {$isNull}";
            throw new TypeNotValidException($description, 500);
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
     *
     * @param $key
     * @throws TypeNotValidException
     */
    public function setKeyComparison($key)
    {
        if(is_string($key))
        {
            $this->key = $key;
            return;
        }
        throw new TypeNotValidException('The type is not valid in ExistenceValidator#setKeyComparison, string is expected', 500);
    }
}