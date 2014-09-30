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

class SubAttributePanelType extends AttributeType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var SelectionValue $selectionValue */
        $selectionValue = $options['selection_value'];

        /** @var Attribute $subAttribute */
        foreach ($selectionValue->getSubAttributes() as $subAttribute) {
            $dataType = $subAttribute->getDataTypeString();
            $formType = __NAMESPACE__ . '\\' . ucfirst($dataType) . 'Type';
            if (class_exists($formType)) {
                $builder->add(
                    $subAttribute->getId(),
                    new $formType(),
                    array(
                        'attribute' => $subAttribute,
                        'is_sub_attr' => true,
                        'selection_value' => $selectionValue
                    )
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var Attribute $attribute */
        /** @var SelectionValue $selectionValue */
        $attribute = $options['attribute'];
        $selectionValue = $options['selection_value'];
        $view->vars['panel_id'] = 'field-'.$attribute->getId().'-panel-'.($selectionValue->getId());
        $constraints = $attribute->getConstraints();
        $view->vars['parent_selection_type'] = $constraints['select_type'];

    }


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'subattributes_panel_collector';
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'selection_value'  => null,
                'attribute'        => null,
                'is_mds' => 0
            )
        );
    }

} 