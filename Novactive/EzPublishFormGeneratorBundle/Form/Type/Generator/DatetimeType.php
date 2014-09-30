<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;

use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatetimeType extends BaseType
{

    const FIELDTYPE_STRING = 'Date';

    protected $attribute_id;

    protected $dynamic_name;

    public function __construct($attribute_id, $dynamic_name = true)
    {
        $this->attribute_id = $attribute_id;
        $this->dynamic_name = $dynamic_name;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('placement', 'hidden', array('required' => false, 'attr' => array('class' => 'rank')))
            ->add(
                'name',
                'text',
                array(
                    'label' => 'Libellé',
                    'required' => false,
                    'constraints' => array(
                        new NotNull(array('message' => 'Le libellé ne peut être vide'))
                    )
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
                    'attr' => array('class' => 'attr_'.$this->attribute_id )
                )
            );
    }

    public function getName()
    {
        if ($this->dynamic_name) {
            return 'attribute_datetime_' . $this->attribute_id;
        } else {
            return '_attribute_datetime';
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\Attribute',
                'block_name' => 'attribute_datetime'
            )
        );
    }

} 