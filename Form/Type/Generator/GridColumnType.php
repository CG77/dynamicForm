<?php


namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Grid column
 *
 * @author m.monsang <m.monsang@novactive.com>
 */
class GridColumnType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('type', 'choice',
                array(
                    'choices' => array(
                        'checkbox'  => 'Checkbox',
                        'radio'     => 'Radio',
                        'text'      => 'Texte',
                    ),
                    'attr'  => array(
                        'class' => 'grid_item_type_selector'
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
        return 'grid_column';
    }


} 