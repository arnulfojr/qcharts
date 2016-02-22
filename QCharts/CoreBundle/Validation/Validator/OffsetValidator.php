<?php

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\OffLimitsException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;
use Symfony\Component\Config\Tests\Loader\Validator;

class OffsetValidator implements ValidatorInterface
{
    private $offset;
    private $limits;
    private $key = "Offset";

    /**
     * @return bool
     * @throws OffLimitsException
     */
    public function validate()
    {
        if (is_null($this->offset) || !is_numeric($this->offset) || $this->offset < 0)
        {
            throw new OffLimitsException("The offset is not set or is not numeric, default: 0", 500);
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->offset;
    }

    /**
     * @param mixed $object
     * @throws TypeNotValidException
     */
    public function setObject($object)
    {
        $this->offset = $object;
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
        $this->key = $key;
    }
}