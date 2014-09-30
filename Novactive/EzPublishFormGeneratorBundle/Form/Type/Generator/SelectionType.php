<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;


use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SelectionType extends BaseType
{

    const MAX_NESTING_LEVEL = 3;
    const FIELDTYPE_STRING = 'Zone de sélection';

    protected $attributeId;

    protected $dynamic_name;

    public function __construct($attributeId, $dynamic_name = true)
    {
        $this->attributeId = $attributeId;
        $this->dynamic_name = $dynamic_name;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $nestingLevel = $options['nesting_level'];
        $aConstraints = array('select_type');

        $builder

            ->add('constraints', new ConstraintType($aConstraints, 'select', $this->attributeId), array())

            ->add('placement', 'hidden', array('required' => false, 'attr' => array('class' => 'rank')))


            ->add(
                'name',
                'text',
                array(
                    'label' => 'Libellé',
                    'required' => false,
                    'constraints' => new NotNull(array('message' => 'Le libellé ne peut être vide')),
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'required' => false
                )
            )
            ->add(
                'identifier',
                'text',
                array(
                    'required' => false,
                    'label' => 'Identifiant',
                )
            )
            ->add(
                'is_collector',
                'checkbox',
                array(
                    'required' => false,
                    'label' => 'Collecteur d\'information ?',
                    'label_attr' => array('class' => 'page_name'),
                    'attr' => array('class' => 'page_name')

                )
            )
            ->add(
                'is_required',
                'checkbox',
                array(
                    'required' => false,
                    'label' => 'Obligatoire ?'
                )
            )
            ->add(
                "illustration_object_id",
                "hidden",
                array(
                    "required" => false,
                    'attr' => array('class' => 'attr_'.$this->attributeId )
                )
            )
            ;

        if ($nestingLevel < self::MAX_NESTING_LEVEL) {
            $builder
                ->add(
                    'selectionValues',
                    'collection',
                    array(
                        'type' => new SelectionValueType($this->attributeId),
                        'required' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'label' => ' ',
                        'attr' => array('class' => 'answers sortable',  'data-level' => $nestingLevel + 1),
                        'options' => array(
                            'nesting_level' => $nestingLevel + 1,
                            'attr' => array('class' => 'answer',)
                        )
                    )
                );
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->dynamic_name) {
            return 'attribute_selection_' . $this->attributeId;
        } else {
            return '_attribute_selection';
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\Attribute',
                'block_name' => 'attribute_selection',
                'nesting_level' => 0,
            )
        );
    }

} 