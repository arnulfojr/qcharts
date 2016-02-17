<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 3:18 PM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\StringRegexInstanceClass;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class NoAsteriscValidator extends StringRegexInstanceClass implements ValidatorInterface
{

    /** @var string $queryString */
    private $queryString;

    /** @var array $limits */
    private $limits;

    private $key = '';

    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        $queryArray = \SqlFormatter::splitQuery($this->queryString);
        $pattern = '/\*/';

        if ($this->stringHasRegexInstance($queryArray, $pattern))
        {
            throw new ValidationFailedException("Query has to have all wished columns defined, no '*' is allowed", 500);
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
        if (!is_string($query))
        {
            throw new TypeNotValidException("The passed query is not an instance of string", 500);
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