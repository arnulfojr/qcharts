<?php

namespace QCharts\CoreBundle\Form;

use Doctrine\Bundle\DoctrineBundle\Registry;
use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Form\ChartConfig\ChartConfig;
use QCharts\CoreBundle\Form\Query\QueryForm;
use QCharts\CoreBundle\Form\Transformer\DirectoryTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QueryRequestType extends AbstractType
{
    /** @var array $options */
    private $options;
    /** @var Registry $doctrine */
    private $doctrine;


    public function __construct(Registry $doctrine, array $options)
    {
        $this->doctrine = $doctrine;
        $this->options = $options;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
            ->add('title')
            ->add('description')
            ->add('query', new QueryForm(), [
                "label"=>false
            ])
            ->add("config", new ChartConfig($this->doctrine, $this->options), [
                "label"=>false
            ])
            ->add('cronExpression', 'hidden')
            ->add('directory', "hidden")
    	    ->add('Save', 'submit', array('label' => 'Save Query', 'attr'=>['class'=>'btn-save btn btn-sm']))
        ;

        $builder->get('directory')->addModelTransformer(new DirectoryTransformer($this->doctrine->getManager()));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $formEvent) {
            $form = $formEvent->getForm();
            /** @var QueryRequest $qr */
            $qr = $formEvent->getData();

            if (isset($qr))
            {
                $form->add('delete', 'button', [
                    'label'=>'Delete',
                    'attr'=>[
                        'class'=>'btn btn-delete btn-sm'
                    ]
                ]);
            }

        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => 'QCharts\CoreBundle\Entity\QueryRequest',
            'csrf_protection' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'query_request';
    }
}
