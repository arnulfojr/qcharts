<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 5:07 PM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class ExistenceValidator implements ValidatorInterface
{

    /** @var string $chartType */
    private $type;

    /** @var array $limits */
    private $limits;

    /** @var string $key */
    private $key = 'Existence';

    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        if (!array_key_exists($this->type, $this->limits[$this->key]))
        {
            throw new ValidationFailedException("The given type for {$this->key} is not supported: {$this->type}", 500);
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->type;
    }

    /**
     * @param mixed $object
     * @throws TypeNotValidException
     */
    public function setObject($object)
    {
        if (!is_string($object))
        {
            throw new TypeNotValidException("The given parameter is not valid", 500);
        }
        $this->type = $object;
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