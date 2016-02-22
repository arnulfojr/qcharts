<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/11/16
 * Time: 11:04 AM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\OffLimitsException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class RowLimitValidator implements ValidatorInterface
{
    private $rows;
    private $limits;
    private $key = 'RowLimit';

    /**
     * @return bool
     * @throws OffLimitsException
     */
    public function validate()
    {
        if (is_null($this->rows) || $this->rows > $this->limits[$this->key])
        {
            throw new OffLimitsException("The amount given of rows is off limit", 500);
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->rows;
    }

    /**
     * @param mixed $object
     * @throws TypeNotValidException
     */
    public function setObject($object)
    {
        $this->rows = $object;
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