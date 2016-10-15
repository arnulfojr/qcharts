<?php

namespace QCharts\CoreBundle\Validation\Validator;

use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\StringRegexInstanceClass;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

/**
 * Class ValidTableNameValidator
 * @package QCharts\CoreBundle\Validation\Validator
 */
class ValidTableNameValidator extends StringRegexInstanceClass implements ValidatorInterface
{
    /* This pattern gets any space pattern */
    const ANY_SPACE_META_SEQUENCE_PATTERN = '(\s+)';
    /** @var string $queryString */
    private $queryString;
    /** @var array $limits */
    private $limits;
    /** @var string $key */
    private $key;
    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        $arrayOfQueries = \SqlFormatter::splitQuery($this->queryString);

        $queryString = array_map("strtolower", $arrayOfQueries);
        $tableNames = array_map("strtolower", $this->limits["table_names"]);
        $tableNamesString = $this->getTableNamesStringForRegex($tableNames);

        $regex = "/(from".ValidTableNameValidator::ANY_SPACE_META_SEQUENCE_PATTERN."({$tableNamesString}))/";
        $hasJoinCommand = "/join/";
        if ($this->stringHasRegexInstance($queryString, $hasJoinCommand))
        {
            $regexJoinCommand = "/(join".ValidTableNameValidator::ANY_SPACE_META_SEQUENCE_PATTERN."({$tableNamesString}))/";
            $result = $this->stringHasRegexInstance($queryString, $regex)
                && $this->stringHasRegexInstance($queryString, $regexJoinCommand);
            if (!$result)
            {
                throw new ValidationFailedException("The given query does not contain a valid table in the Join statement.", 500);
            }
            return $result;
        }

        if (!$this->stringHasRegexInstance($queryString, $regex))
        {
            $helper = "<em>Check that the tables specify the schema</em>";

            throw new ValidationFailedException("<p>The given query does not contain a valid table.</p>{$helper}", 500);
        }
        return true;

    }

    /**
     * @param array $tableNames
     * @return string
     */
    private function getTableNamesStringForRegex(array $tableNames = [])
    {
        $tableNameString = implode('\b|\b', $tableNames);
        $tableNameString = "\\b{$tableNameString}\\b";
        return $tableNameString;
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