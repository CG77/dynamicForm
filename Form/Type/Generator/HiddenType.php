<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;


use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HiddenType extends BaseType
{

    const FIELDTYPE_STRING = 'Ligne de texte / Champ caché';

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
        $aConstraints = array();
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
                'identifier',
                'text',
                array(
                    'required' => false,
                    'label' => 'Identifiant',
                )
            )
            ->add('data_type_string', 'hidden', array('required' => false, 'data' => 'hidden'))
            ->add('constraints', new ConstraintType($aConstraints, 'hidden', $this->attributeId), array())
        ;
    }

    public function getName()
    {
        if ($this->dynamicName) {
            return 'attribute_hidden_' . $this->attributeId;
        } else {
            return '_attribute_hidden';
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\Attribute',
                'block_name' => 'attribute_hidden'
            )
        );
    }

} 