<?php

namespace Novactive\EzPublishFormGeneratorBundle\Twig;

use Novactive\EzPublishFormGeneratorBundle\Storage\FileService;
use Symfony\Component\Form\FormView;
use Twig_Extension;

class FormExtension extends Twig_Extension
{

    /**
     * @var array
     */
    protected $availableTypes = array();

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var \Novactive\EzPublishFormGeneratorBundle\Storage\FileService
     */
    protected $fileService;

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param $availableTypes
     * @param FileService $fileService
     */
    public function __construct($availableTypes, FileService $fileService)
    {
        $this->availableTypes = $availableTypes;
        $this->fileService = $fileService;
    }

    public function getFunctions()
    {
        return array(
            'nova_ezform_allowed_file_extensions' => new \Twig_Function_Method($this, 'getAllowedFileExtensions', array()),
        );
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return array(
            'nova_ezform_sort_subattributes' => new \Twig_Filter_Method($this, 'sortSubAttributes'),
            'nova_ezform_render_attribute' => new \Twig_Filter_Method($this, 'renderAttribute', array('is_safe' => array('html'))),
            'nova_ezform_file_info' => new \Twig_Filter_Method($this, 'getFileInfo', array()),
        );
    }

    /**
     * @return array
     */
    public function getAllowedFileExtensions()
    {
        return $this->fileService->getAllowedExtensions();
    }

    /**
     * @inheritdoc
     */
    public function sortSubAttributes(FormView $form)
    {
        $widgets = array();

        foreach ($this->availableTypes as $type) {
            $formChild = "subAttributes" . ucfirst($type);

            if(!empty($form[$formChild])){
                /** @var FormView $child */
                foreach ($form[$formChild] as $child) {
                    $rank = (int)$child['placement']->vars['value'];
                    if (array_key_exists($rank, $widgets)) {
                        $rank = rand();
                    }
                    $widgets[$rank] = $child;
                }
            }
        }
        ksort($widgets);

        return $widgets;
    }

    public function renderAttribute(array $attribute)
    {
        $dataType = $attribute['data_type'];
        $template = "NovactiveEzPublishFormGeneratorBundle:FormView/AttributeType:" . $dataType . '.html.twig';

        return $this->environment->render($template, array('attribute' => $attribute));
    }

    public function getFileInfo($value)
    {
        try {
            if ($value) {
                return $this->fileService->getFileInfos(unserialize($value));
            }
        } catch (\Exception $ex) {

        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'nova_ezformgenerator_extension';
    }

} 