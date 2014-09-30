<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class GridDefinitionType extends AbstractType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('row', 'collection',
                array(
                    'type' => new GridRowType(),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype_name' => '__grid_row__',
                    'by_reference' => false,
                )
            )
            ->add('column', 'collection',
                array(
                    'type' => new GridColumnType(),
                    'required' => false,
                    'allow_add' => true,
                    'prototype_name' => '__grid_column__',
                    'allow_delete' => true,
                    'by_reference' => false,
                )
            )
        ;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'grid_definition';
    }
}