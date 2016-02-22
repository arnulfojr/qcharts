<?php

namespace QCharts\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRegistrationType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
    }

    /**
     * @return string
     */
    public function getParent()
    {
        // if using symfony greater than 2.8, change also the config fos_user
        // return 'FOS\UserBundle\Form\Type\RegistrationFormType';
        return 'fos_user_registration';
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'core_user_registration_form';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}