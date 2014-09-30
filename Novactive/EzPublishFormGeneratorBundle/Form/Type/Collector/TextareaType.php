<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;


use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextAreaType extends AttributeType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attribute = $options['attribute'];
        $isSubAttr = $options["is_sub_attr"];
        $selectionValue = $options["selection_value"];
        $isMds = $options['is_mds'];
        $options = $this->getAttributeOptions($attribute);

        $constraints = $attribute->getConstraints();
        if($isMds){
            $options['attr']['class'] = $attribute->getIdentifier();
        }
        if (!empty($constraints['nb_line'])) {
            $options['attr']['rows'] = $constraints['nb_line'];
        }
        if (!empty($constraints['nb_column'])) {
            $options['attr']['rows'] = $constraints['nb_column'];
        }

        $builder
            ->add(
                'value',
                'textarea',
                $options
            );
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $ev) use ($options,$isSubAttr,$selectionValue) {
                $form = $ev->getForm();
                $data = $ev->getData();
                $value = $data->getValue();
                if(!$isSubAttr){
                    if (!$value && $options['required']) {
                        $form->get('value')->addError(new FormError(sprintf("Ce champ est requis")));
                        $form->addError(new FormError(sprintf("Ce champ est requis")));
                    }
                }else{
                    if($this->isSubAttributeRequired($selectionValue, $form->getParent()->getParent()
                        ->getParent()->get('selection')->getData())){
                        if (!$value && $options['required']) {
                            $form->get('value')->addError(new FormError(sprintf("Ce champ est requis")));
                            $form->addError(new FormError(sprintf("Ce champ est requis")));
                        }
                    }
                }
            }
        );
    }

    public function getName()
    {
        return 'textarea_collector';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\CollectionAttributeValue',
                'attribute'  => null,
                'is_sub_attr' => false,
                'selection_value' => null,
                'is_mds' => 0
            )
        );
    }

} 