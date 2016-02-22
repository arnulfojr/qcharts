<?php

namespace QCharts\CoreBundle\Form\ChartConfig;


use Doctrine\Bundle\DoctrineBundle\Registry;
use QCharts\CoreBundle\Form\Transformer\DatabaseConnectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChartConfig extends AbstractType
{

    private $doctrine;
    private $chartTypes;

    public function __construct(Registry $doctrine, array $chartTypes)
    {
        $this->doctrine = $doctrine;
        $this->chartTypes = $chartTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add("databaseConnection", 'choice', [
                'label'=>"Connection",
                "multiple" => false,
                "choices"=> $this->getConnections()
            ])
            ->add('typeOfChart', 'choice', [
                "multiple" => false,
                "choices"=>$this->chartTypes
            ])
            ->add("queryLimit")
            ->add('executionLimit')
            ->add("offset")
            ->add("isCached", 'choice', [
                "label" => "Execution mode",
                "choices" => [
                    0=>"Live",
                    1=>"Cached",
                    2=>"Time Machine"
                ]
            ])
        ;

        $builder->get("databaseConnection")->addModelTransformer(new DatabaseConnectionTransformer($this->doctrine));
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        $rawConnections = $this->doctrine->getConnectionNames();
        $connections = [];
        foreach ($rawConnections as $key=>$value)
        {
            $connections[$key] = $key;
        }
        return $connections;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            "data_class" => 'QCharts\CoreBundle\Entity\ChartConfig',
            'csrf_protection' => false,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "";
    }
}