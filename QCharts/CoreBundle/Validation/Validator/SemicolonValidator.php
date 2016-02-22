<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 3:24 PM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\StringRegexInstanceClass;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class SemicolonValidator extends StringRegexInstanceClass implements ValidatorInterface
{

    /** @var array $limits */
    private $limits;
    /** @var string $queryString */
    private $queryString;
    /** @var string $key */
    private $key;

    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        if ($this->wholeStringHasRegexInstance($this->queryString))
        {
            throw new ValidationFailedException("Query has semicolon, please remove all semicolons in the Query.", 500);
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
     * @param string $query
     * @throws TypeNotValidException
     */
    public function setObject($query)
    {
        if(!is_string($query))
        {
            throw new TypeNotValidException("Query passed is not a string", 500);
        }
        $this->queryString = $query;
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