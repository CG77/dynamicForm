<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Collector;


use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Novactive\EzPublishFormGeneratorBundle\Storage\FileService;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileType extends AttributeType
{
    /**
     * @var FileService
     */
    protected $storage;

    /**
     * @param FileService $storage
     */
    function __construct(FileService $storage)
    {
        $this->storage = $storage;
    }


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

        $options['constraints'] = array();

        $builder
            ->add(
                'value',
                'file',
                $options
            );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $ev) use ($options,$isSubAttr,$selectionValue) {
                $form = $ev->getForm();
                $data = $ev->getData();
                if ($data) {
                    $file = $data->getValue();
                    if ($file instanceof UploadedFile) {
                        if (!$this->storage->checkExtension($file)) {
                            $form->get('value')->addError(
                                new FormError(sprintf("Le type de fichier %s n'est pas supporté",
                                    $file->getClientOriginalExtension())
                                ));
                            $form->addError(
                                new FormError(sprintf("Le type de fichier %s n'est pas supporté",
                                        $file->getClientOriginalExtension())
                                ));
                            return;
                        }
                    }
                }
                if(!$isSubAttr){
                    if($data->getValue() === null && $options['required']){
                        $form->get('value')->addError(new FormError(sprintf("Ce champ est requis")));
                        $form->addError(new FormError(sprintf("Ce champ est requis")));
                        return;
                    }
                }else{
                    if($this->isSubAttributeRequired($selectionValue, $form->getParent()->getParent()
                        ->getParent()->get('selection')->getData())){
                        if($data->getValue() === null && $options['required']){
                            $form->get('value')->addError(new FormError(sprintf("Ce champ est requis")));
                            $form->addError(new FormError(sprintf("Ce champ est requis")));
                            return;
                        }
                    }
                }

            }
        );
    }

    public function getName()
    {
        return 'file_collector';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Novactive\EzPublishFormGeneratorBundle\Entity\CollectionAttributeValue',
                'attribute' => null,
                'is_sub_attr' => false,
                'selection_value' => null
            )
        );
    }

} 