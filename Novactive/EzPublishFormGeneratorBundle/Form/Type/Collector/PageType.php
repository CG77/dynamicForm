<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;

use Novactive\EzPublishFormGeneratorBundle\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Build form collector
 *
 * @author m.monsang <m.monsang@novactive.com>
 */
class PageType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Page $page */
        $page = $options['page'];
        foreach ($page->getAttributes() as $attribute) {
            $dataType = $attribute->getDataTypeString();

            if ($dataType == 'file') {
                $formType = 'file_collector';
                $builder->add(
                    $attribute->getId(),
                    $formType,
                    array('attribute' => $attribute)
                );
            }elseif($dataType == 'datetime'){
                $builder->add(
                    $attribute->getId(),
                    new DateTimeType(),
                    array('attribute' => $attribute)
                );
            }else{
                $formType = __NAMESPACE__ . '\\' . ucfirst($dataType) . 'Type';
                if (class_exists($formType)) {
                    $builder->add(
                        $attribute->getId(),
                        new $formType(),
                        array('attribute' => $attribute,'is_mds'=>$options['is_mds'])
                    );
                }
            }
        }
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'form_collector_page';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        /** @var Page $page */
        $page = $options['page'];

        $view->vars['title'] = $page->getDataText();
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'page' => null,
                'is_mds' => 0
            )
        );
    }


}