<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/3/16
 * Time: 10:30 AM
 */

namespace QCharts\CoreBundle\Validation\Validator;


use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;

class CronExpressionValidator implements ValidatorInterface
{
    /** @var array $limits */
    private $limits;
    /** @var string $expression */
    private $expression;
    /** @var string $key */
    private $key = "CronExpression";
    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate()
    {
        $regex = '/(28|\*) (2|\*) (7|\*) (1|\*) (1|\*)/';
        if (preg_match_all($regex, $this->expression) == 1)
        {
            throw new ValidationFailedException("Expression was not valid expression give: {$this->expression}", 500);
        }
        return true;
    }

    /**
     * @return string
     */
    public function getObject()
    {
        return $this->expression;
    }

    /**
     * @param string $object
     * @throws TypeNotValidException
     */
    public function setObject($object)
    {
        if (!is_string($object))
        {
            $description = ExceptionMessage::TYPE_NOT_VALID("CronExpressionValidator was not given an string
            value at the setObject function");
            throw new TypeNotValidException($description, 500);
        }
        $this->expression = $object;
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