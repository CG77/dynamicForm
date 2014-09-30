<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;


use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Novactive\EzPublishFormGeneratorBundle\Entity\CollectionAttributeValue;
use Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator\GridColumnType;
use Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator\GridRowType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GridDefinitionType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $definition = $options['definition'];
        $x = 0;
        foreach ($definition['column'] as $columnDefinition) {
            $y = 0;
            foreach ($definition['row'] as $rowDefinition) {
                if (isset($rowDefinition['defaults'][$x])) {
                    $rowDefaultValue = $rowDefinition['defaults'][$x];
                    $key = $x . ':' . $y;
                    $collectionValue = new CollectionAttributeValue();
                    $value = $rowDefaultValue['value'];
                    if (in_array($rowDefaultValue['type'], array('checkbox', 'radio'))) {
                        $value = ($value == 1);
                    }

                    $collectionValue->setValue($value);
                    $collectionValue->setKey($key);
                    $builder
                        ->add(
                            $key,
                            new GridTupleType(),
                            array(
                                'widget' => $rowDefaultValue['type'],
                                'data' => $collectionValue,
                                'key' => $key,
                            )
                        );
                }
                ++$y;
            }
            ++$x;
        }
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $definition = $options['definition'];
        $x = 0;
        $column = $row = array();

        foreach ($definition['column'] as $columnDefinition) {
            $column[] = $columnDefinition['name'];
        }
        foreach ($definition['row'] as $rowDefinition) {
            $row[] = $rowDefinition['name'];
        }
        $view->vars['column'] = $column;
        $view->vars['row'] = $row;
    }


    public function getName()
    {
        return 'grid_collector_definition';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'definition' => null,
                'is_sub_attr' => false,
                'selection_value' => null,
                'is_mds' => 0
            )
        );
    }

} 