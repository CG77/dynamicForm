<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;


use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Novactive\EzPublishFormGeneratorBundle\Entity\SelectionValue;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SelectionType extends AttributeType
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
        $currentSelectValue = $options["selection_value"];
        $options = $this->getAttributeOptions($attribute);

        $choices = array();
        /** @var SelectionValue $selectionValue */
        foreach ($attribute->getSelectionValues() as $key => $selectionValue) {
            $choices[$selectionValue->getValue()] = $selectionValue->getLibelle();

        }
        $options['choices'] = $choices;

        $options['attr'] = array();
        $constraints = $attribute->getConstraints();
        $selectType = $constraints['select_type'];

        $options['empty_value'] = false;
        switch ($selectType) {
            case 'multiple':
                $options['expanded'] = false;
                $options['multiple'] = true;
                $options['attr']['class'] = 'multiselect';
                break;
            case 'checkbox':
                $options['expanded'] = true;
                $options['multiple'] = true;
                $options['attr']['class'] = 'singleselect';
                break;
            case 'radio':
                $options['expanded'] = true;
                $options['multiple'] = false;
                $options['attr']['class'] = '';
                break;
            case 'select':
            default:
                $options['expanded'] = false;
                $options['multiple'] = false;
                $options['attr']['class'] = 'select';
                $options['empty_value'] = '';
                break;

        }
        $options['attr']['data-selection-type'] = $selectType;
        $options['attr']['illustration'] = $attribute->getIllustrationObjectId();
        $builder
            ->add(
                'selection',
                'choice',
                $options
            )
        ;

        $builder->add('subAttributes', new SubAttributeType(), array('attribute' => $attribute));

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $ev) use ($options,$isSubAttr,$currentSelectValue) {
                $form = $ev->getForm();
                $data = $ev->getData();
                $value = $data['selection'];
                if(!$isSubAttr){
                    if (!$value && $options['required']) {
                        $form->get('selection')->addError(new FormError(sprintf("Ce champ est requis")));
                        $form->addError(new FormError(sprintf("Ce champ est requis")));
                    }
                }else{
                    if($this->isSubAttributeRequired($currentSelectValue, $form->getParent()->getParent()
                        ->getParent()->get('selection')->getData())){
                        if (!$value && $options['required']) {
                            $form->get('selection')->addError(new FormError(sprintf("Ce champ est requis")));
                            $form->addError(new FormError(sprintf("Ce champ est requis")));
                        }
                    }
                }
            }
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        /** @var Attribute $attribute */
        $attribute = $options['attribute'];
        $constraints = $attribute->getConstraints();
        $selectType = $constraints['select_type'];
        switch ($selectType) {
            case 'multiple':
                $view->vars['parent_class'] = 'multiSelectGroup nestedGroup tabGroup';
                break;
            case 'checkbox':
                $view->vars['parent_class'] = 'checkerGroup nestedGroup collapseGroup';
                break;
            case 'radio':
                $view->vars['parent_class'] = 'radioGroup nestedGroup tabGroup';
                break;
            case 'select':
            default:
                $view->vars['parent_class'] = 'singleSelectGroup nestedGroup tabGroup';
                break;
        }

        $targets = array();
        /** @var SelectionValue $selectionValue */
        foreach ($attribute->getSelectionValues() as $key => $selectionValue) {
            $targets[$selectionValue->getValue()] = 'field-'.$attribute->getId().'-panel-'.($selectionValue->getId());
        }
        $view->vars['targets'] = $targets;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'selection_collector';
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'attribute'  => null,
                'parent_class' => null,
                'is_sub_attr' => false,
                'selection_value' => null,
                'is_mds' => 0
            )
        );
    }

} 