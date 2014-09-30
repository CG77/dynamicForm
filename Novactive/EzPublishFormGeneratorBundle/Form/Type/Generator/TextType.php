<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;


use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextType extends BaseType
{

    const FIELDTYPE_STRING = 'Champ texte';

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
     * @param FormBuilderInterface $builder
     * @param array $options
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
            ->add('data_type_string', 'hidden', array('required' => false, 'data' => 'text'))
            ->add('constraints', new ConstraintType($aConstraints, 'text', $this->attributeId), array())
            ->add(
                "illustration_object_id",
                "hidden",
                array(
                    "required" => false,
                    'attr' => array('class' => 'attr_'.$this->attributeId )
                )
            );
    }

    public function getName()
    {
        if ($this->dynamicName) {
            return 'attribute_text_' . $this->attributeId;
        } else {
            return '_attribute_text';
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\Attribute',
                'block_name' => 'attribute_text'
            )
        );
    }

} 