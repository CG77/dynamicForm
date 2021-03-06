<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;


use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DateTimeType extends AttributeType
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
        $options = $this->getAttributeOptions($attribute);
        $options['widget']  = 'single_text';
        $options['format']  = 'd/M/y';

        $builder
            ->add(
                'value',
                'date',
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
        return 'date_collector';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\CollectionAttributeValue',
                'attribute'  => null,
                'is_sub_attr' => false,
                'selection_value' => null
            )
        );
    }

} 