<?php


namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Grid row
 *
 * @author m.monsang <m.monsang@novactive.com>
 */
class GridRowType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('defaults', 'collection',
                array(
                    'type'              => new GridItemType(),
                    'allow_add'         => true,
                    'allow_delete'      => true,
                    'prototype_name'    => '__grid_row_name__',
                    'options' => array(
                        'attr'              => array(
                            'class'         => 'grid_row_prototype'
                        )
                    )
                )
            )
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'grid_row';
    }


} 