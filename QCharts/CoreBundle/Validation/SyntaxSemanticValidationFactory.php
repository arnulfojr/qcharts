<?php


namespace QCharts\CoreBundle\Validation;

use QCharts\CoreBundle\Validation\ValidationInterface\ValidationFactoryClass;
use QCharts\CoreBundle\Validation\ValidationInterface\ValidatorInterface;
use QCharts\CoreBundle\Validation\Validator\NoAsteriscValidator;
use QCharts\CoreBundle\Validation\Validator\ReadOnlyValidator;
use QCharts\CoreBundle\Validation\Validator\SemicolonValidator;
use QCharts\CoreBundle\Validation\Validator\ValidTableNameValidator;

class SyntaxSemanticValidationFactory extends ValidationFactoryClass
{

    public function registerValidators()
    {
        /** @var array $validators */
        $validators = [
            new ReadOnlyValidator(),
            new NoAsteriscValidator(),
            new ValidTableNameValidator(),
            new SemicolonValidator(),
        ];

        // Register the validators
        $this->validators = $validators;

        $this->addLimitsToValidators();
    }

    public function getValidators()
    {
        return $this->validators;
    }

    protected function addLimitsToValidators()
    {
        foreach ($this->validators as $validator)
        {
            /** @var ValidatorInterface $validator */
            $validator->setLimits($this->limits);
        }
    }
}