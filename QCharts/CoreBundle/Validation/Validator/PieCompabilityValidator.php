<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 4:53 PM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class PieCompabilityValidator implements ValidatorInterface
{

    /** @var array $limits */
    private $limits;
    /** @var array $results */
    private $object;

    private $key = 'PieCompability';

    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        $hasColumns = (count($this->object["results"]) == 2);

        if (!$hasColumns && $this->object["chartType"] == 'pie')
        {
            $hasColumns = count(current($this->object["results"]));
            if ($hasColumns > 2)
            {
                throw new ValidationFailedException("The results have more columns than allowed, for Pie Charts only 2 columns are allowed", 500);
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param array $object
     * @throws TypeNotValidException
     */
    public function setObject($object)
    {
        if (!is_array($object))
        {
            throw new TypeNotValidException("The given element is not an array, Pie Chart Compatible Validator", 500);
        }
        $this->object = $object;
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
        if (is_string($key))
        {
            $this->key = $key;
            return;
        }
        throw new TypeNotValidException('The type is not valid in ExistenceValidator#setKeyComparison, string is expected', 500);
    }
}