<?php


namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Grid item
 *
 * @author m.monsang <m.monsang@novactive.com>
 */
class GridItemType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'hidden', array('attr' => array('class' => 'type')))
            ->add('value', 'text',
                array(
                    'attr' => array('class' => 'value'),
                    'required' => false,
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
        return 'grid_item';
    }


} 