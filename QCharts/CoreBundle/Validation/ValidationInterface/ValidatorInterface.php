<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 10:03 AM
 */

namespace QCharts\CoreBundle\Validation\ValidationInterface;


use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;

interface ValidatorInterface
{
    /**
     * @return bool
     * @throws ValidationFailedException
     */
    public function validate();

    /**
     * @return mixed
     */
    public function getObject();

    /**
     * @param mixed $object
     * @throws TypeNotValidException
     */
    public function setObject($object);

    /**
     * @return array
     */
    public function getLimits();

    /**
     * @param array $limits
     */
    public function setLimits(array $limits);

    /**
     * Sets the key used to call the value from the limits array
     * @param $key
     * @throws TypeNotValidException
     */
    public function setKeyComparison($key);

}