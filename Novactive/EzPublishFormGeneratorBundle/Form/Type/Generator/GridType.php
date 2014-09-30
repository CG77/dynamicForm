<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;


use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GridType extends BaseType
{

    const FIELDTYPE_STRING = 'Matrice';

    protected $attributeId;

    protected $dynamicName;

    /**
     * @param $attributeId
     * @param bool $dynamicName
     */
    public function __construct($attributeId, $dynamicName = true)
    {
        $this->attributeId = $attributeId;
        $this->dynamicName = $dynamicName;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $aConstraints = array('max_length');
        $builder
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
            ->add('data_type_string', 'hidden', array('required' => false, 'data' => 'grid'))
            ->add('data_text', new GridDefinitionType(), array('required' => false))
            ->add('constraints', new ConstraintType($aConstraints, 'grid', $this->attributeId), array())
            ->add(
                "illustration_object_id",
                "hidden",
                array(
                    "required" => false,
                    'attr' => array('class' => 'attr_'.$this->attributeId )
                )
            );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        if ($this->dynamicName) {
            return 'attribute_grid_' . $this->attributeId;
        } else {
            return '_attribute_grid';
        }
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\Attribute',
                'block_name' => 'attribute_grid'
            )
        );
    }

} 