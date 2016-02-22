<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 5:16 PM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\NotPlotableException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\ResultFormatter\ResultsPrepareFormatter;
use QCharts\CoreBundle\Service\QueryResultsFormatter;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class NumericListValidator implements ValidatorInterface
{

    /** @var array $values */
    private $values;
    /** @var array $limits */
    private $limits;
    /** @var string $key */
    private $key = 'NumericList';

    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        try
        {
            // results come from the data base, hence is an array of mapped array
            if ($this->limits[$this->key] == 'table')
            {
                return true;
            }
            if ($this->limits[$this->key] == 'pie')
            {
                $formatter = new ResultsPrepareFormatter();
                $values = $formatter->prepareResults($this->values);
                $values = end($values);
                array_map([$this, 'isValueNumeric'], $values);
                return true;
            }

            try
            {
                array_map([$this, 'isValueNumeric'], $this->values);
                return true;
            }
            catch (NotPlotableException $e)
            {
                $formatter = new ResultsPrepareFormatter();
                $values = $formatter->prepareResults($this->values);
                $values = next($values);
                array_map([$this, 'isValueNumeric'], $values);
                return true;
            }
        }
        catch (NotPlotableException $e)
        {
            throw new ValidationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $value
     * @return bool
     * @throws NotPlotableException
     */
    public function isValueNumeric($value)
    {
        if (!is_numeric($value))
        {
            throw new NotPlotableException("When ploting {$this->limits['NumericList']} Charts columns, except the most right column, should be numeric values", 405);
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->values;
    }

    /**
     * @param mixed $object
     * @throws TypeNotValidException
     */
    public function setObject($object)
    {
        if (!is_array($object))
        {
            throw new TypeNotValidException("Object is not an array", 500);
        }
        $this->values = $object;
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