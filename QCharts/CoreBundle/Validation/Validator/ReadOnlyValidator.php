<?php

namespace QCharts\CoreBundle\Validation\Validator;

use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\StringRegexInstanceClass;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;
use \SqlFormatter;

class ReadOnlyValidator extends StringRegexInstanceClass implements ValidatorInterface
{

    /** @var  string $queryString */
    private $queryString;
    /** @var  array limits */
    private $limits;

    private $key = 'ReadOnly';

    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        $arrayOfQueries = SqlFormatter::splitQuery($this->queryString);
        $arrayOfQueries = array_map("strtolower", $arrayOfQueries);
        $regex = "/update|insert|truncate|delete/";
        if ($this->stringHasRegexInstance($arrayOfQueries, $regex)) {
            throw new ValidationFailedException("Query is not Read-Only", 500);
        }

        return true;
    }

    /**
     * @return string
     */
    public function getObject()
    {
        return $this->queryString;
    }

    /**
     * @param string $queryString
     * @throws TypeNotValidException
     */
    public function setObject($queryString)
    {
        if (!is_string($queryString))
        {
            throw new TypeNotValidException("The passed query is not an instance of string", 500);
        }
        $this->queryString = $queryString;
    }

    public function getLimits()
    {
        return $this->limits;
    }

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