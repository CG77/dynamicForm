<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;


use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Novactive\EzPublishFormGeneratorBundle\Entity\SelectionValue;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubAttributeType extends AttributeType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Attribute $attribute */
        $attribute = $options['attribute'];

        foreach ($attribute->getSelectionValues() as $selectionValue) {
            $builder->add(
                $selectionValue->getId(),
                new SubAttributePanelType(),
                array(
                    'selection_value' => $selectionValue,
                    'attribute'       => $attribute
                )
            );



        }
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attribute = $options['attribute'];
        $constraints = $attribute->getConstraints();
        $view->vars['parent_selection_type'] = $constraints['select_type'];
    }


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'subattributes_collector';
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'attribute'  => null,
                'is_mds' => 0
            )
        );
    }

} 