<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;


use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AttributeType extends BaseType
{

    public function isSubAttributeRequired($oSelection,$values){
        if(is_array($values)){
            foreach ($values as $value){
                if($oSelection->getValue() === $value){
                    return true;
                }
            }
        }else{
            if($oSelection->getValue() === $values){
                return true;
            }
        }
        return false;
    }

    /**
     * @param Attribute $attribute
     * @return array
     */
    public function getAttributeOptions(Attribute $attribute)
    {
        $options = array(
            'label' => $attribute->getName(),
            'required' => $attribute->getIsRequired(),
            'attr' => array(
                'data-description' => $attribute->getDescription(),
                'illustration' => $attribute->getIllustrationObjectId()
            )
        );
        return $options;
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        /** @var Attribute $attribute */
        $attribute = $options['attribute'];
        $view->vars['label'] = $attribute->getName();
        $view->vars['is_collector'] = $attribute->getIsCollector();
        $view->vars['description'] = $attribute->getDescription();
        $view->vars['illustration'] = $attribute->getIllustrationObjectId();
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\CollectionAttributeValue',
                'is_mds' => 0
            )
        );

        $resolver->setRequired(
            array(
                'attribute'
            )
        );
    }

} 