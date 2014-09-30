<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;

use Novactive\EzPublishFormGeneratorBundle\Entity\Entity;
use Novactive\EzPublishFormGeneratorBundle\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Build form collector
 *
 * @author m.monsang <m.monsang@novactive.com>
 */
class FormCollectorBuilderType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $options['entity'];
        if (!$entity instanceof Entity) {
            return;
        }
        /** @var Page $page */
        foreach ($entity->getPages() as $page) {
            $builder->add($page->getId(), new PageType(), array('page' => $page,'is_mds' => $options['is_mds']));
        }
        if($options['captcha']){
            $builder->add('captcha', 'genemu_recaptcha',array('label'=>'Captcha','mapped' => false,
                'attr'=>array('countPages' => $options['countPages'])
            ));
        }
        $builder->add('is_mds', 'hidden',array('data'=>$options['is_mds'],'mapped' => false));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'form_collector_builder';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'entity' => null,
            'captcha' => 0,
            'is_mds' => 0,
            'countPages' => 0
        ));
    }


}