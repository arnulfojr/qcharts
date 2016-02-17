<?php

namespace QCharts\CoreBundle\Form\Directory;


use Doctrine\Bundle\DoctrineBundle\Registry;
use QCharts\CoreBundle\Form\Transformer\DirectoryTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DirectoryType extends AbstractType
{
    /** @var Registry $doctrine */
    private $doctrine;

    /**
     * DirectoryType constructor.
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('parent', 'hidden')
            ->add('Add', 'submit', [
                'attr' => [
                    'class' => 'btn btn-sm btn-default-arny'
                ]
            ])
            ;

        $builder->get('parent')->addModelTransformer(new DirectoryTransformer($this->doctrine->getManager()));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class'=>'QCharts\CoreBundle\Entity\Directory',
            'csrf_protection'=>false
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "directory";
    }
}