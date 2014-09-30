<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;


use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextType extends AttributeType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Attribute $attribute */
        $attribute = $options['attribute'];
        $isSubAttr = $options["is_sub_attr"];
        $selectionValue = $options["selection_value"];
        $isMds = $options['is_mds'];
        $options = $this->getAttributeOptions($attribute);

        if($isMds){
            $options['attr']['class'] = $attribute->getIdentifier();
        }
        $builder
            ->add(
                'value',
                'text',
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
        return 'text_collector';
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