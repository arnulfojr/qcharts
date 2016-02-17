<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 4:50 PM
 */

namespace QCharts\CoreBundle\Validation;


use Cron\CronExpression;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidationFactoryClass;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;
use QCharts\CoreBundle\Validation\Validator\ChartTypeValidator;
use QCharts\CoreBundle\Validation\Validator\CronExpressionValidator;
use QCharts\CoreBundle\Validation\Validator\ExecutionTimeConfigurationValidator;
use QCharts\CoreBundle\Validation\Validator\ExistenceValidator;
use QCharts\CoreBundle\Validation\Validator\NumericListValidator;
use QCharts\CoreBundle\Validation\Validator\OffsetValidator;
use QCharts\CoreBundle\Validation\Validator\PieCompabilityValidator;
use QCharts\CoreBundle\Validation\Validator\RowLimitValidator;
use QCharts\CoreBundle\Validation\Validator\TimeExecutionValidator;

class ChartValidationFactory extends ValidationFactoryClass
{

    public function registerValidators()
    {
        $validators = [
            "PieCompability" => new PieCompabilityValidator(),
            "Existence" => new ExistenceValidator(),
            "TimeExecution" => new TimeExecutionValidator(),
            "RowLimit"=> new RowLimitValidator(),
            "ExecutionTimeConfiguration" => new ExecutionTimeConfigurationValidator(),
            "NumericList" => new NumericListValidator(),
            "Offset" => new OffsetValidator(),
            "CronExpression" => new CronExpressionValidator()
        ];

        //register validators
        $this->validators = $validators;
    }

    /**
     * @return array
     */
    public function getValidators()
    {
        return $this->validators;
    }

    public function addLimitsToValidators()
    {
        foreach ($this->validators as $key=>$validator)
        {
            /** @var ValidatorInterface $validator */
            $validator->setLimits($this->limits);
        }
    }

    /**
     * @param mixed $descriptor
     * @return mixed
     */
    public function getValidator($descriptor)
    {
        return $this->validators[$descriptor];
    }
}