<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;


use Novactive\EzPublishFormGeneratorBundle\Entity\SelectionValue;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;


class SelectionValueType extends BaseType
{

    protected $attributeId;

    public function __construct($attributeId)
    {
        $this->attributeId = $attributeId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('placement', 'hidden', array('required' => false, 'attr' => array('class' => 'rank')))
            ->add(
                'libelle',
                'text',
                array(
                    'label' => 'Libellé réponse',
                    'required' => false,
                    'constraints' => new NotNull(array('message' => 'Le libellé ne peut être vide')),
                )
            )
            ->add(
                'value',
                'text',
                array(
                    'label' => 'Valeur réponse',
                    'required' => false,
                    'constraints' => new NotNull(array('message' => 'Le libellé ne peut être vide')),
                )
            )
            ->add(
                'subAttributesText',
                'collection',
                array(
                    'type' => new TextType(null, false),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => ' ',
                    'attr' => array(
                        'data-id' => null
                    ),
                    'options' => array(
                        'attr' => array('data-sub-attribute' => true),
                    )
                )
            )
            ->add(
                'subAttributesEmail',
                'collection',
                array(
                    'type' => new EmailType(null, false),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => ' ',
                    'attr' => array(
                        'data-id' => null
                    ),
                    'options' => array(
                        'attr' => array('data-sub-attribute' => true),
                    )
                )
            )
            ->add(
                'subAttributesDate',
                'collection',
                array(
                    'type' => new DatetimeType(null, false),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => ' ',
                    'attr' => array(
                        'data-id' => null
                    ),
                    'options' => array(
                        'attr' => array('data-sub-attribute' => true),
                    )
                )
            )
            ->add(
                'subAttributesInteger',
                'collection',
                array(
                    'type' => new IntegerType(null, false),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => ' ',
                    'attr' => array(
                        'data-id' => null
                    ),
                    'options' => array(
                        'attr' => array('data-sub-attribute' => true),
                    )
                )
            )
            ->add(
                'subAttributesTextarea',
                'collection',
                array(
                    'type' => new TextareaType(null, false),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => ' ',
                    'attr' => array(
                        'data-id' => null
                    ),
                    'options' => array(
                        'attr' => array('data-sub-attribute' => true),
                    )
                )
            )
            ->add(
                'subAttributesGrid',
                'collection',
                array(
                    'type' => new GridType(null, false),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => ' ',
                    'attr' => array(
                        'data-id' => null
                    ),
                    'options' => array(
                        'attr' => array('data-sub-attribute' => true),
                    )
                )
            )
            ->add(
                'subAttributesScale',
                'collection',
                array(
                    'type' => new ScaleType(null, false),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => ' ',
                    'attr' => array(
                        'data-id' => null
                    ),
                    'options' => array(
                        'attr' => array('data-sub-attribute' => true),
                    )
                )
            )
            ->add(
                'subAttributesSelection',
                'collection',
                array(
                    'type' => new SelectionType(null, false),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => ' ',
                    'prototype_name' => '__name__' . $options['nesting_level'] . '__',
                    'attr' => array(
                        'data-id' => null,
                    ),
                    'options' => array(
                        'nesting_level' => $options['nesting_level'],
                        'attr' => array('data-sub-attribute' => true),
                    )
                )
            );
    }

    public function getName()
    {
        return 'answer';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\SelectionValue',
                'label' => ' ',
                'nesting_level' => 0,
            )
        );
    }

} 