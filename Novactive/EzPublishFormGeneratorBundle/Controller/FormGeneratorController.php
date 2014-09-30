<?php

namespace Novactive\EzPublishFormGeneratorBundle\Controller;

use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;

use Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eZSession;

class FormGeneratorController extends Controller
{

    protected $aForm = null;

    /**
     * @param $aPages
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEntityAction($aPages,$entityId,$contentId)
    {

        $options = array('csrf_protection' => false);
        $originalTemplateName = '';
        $filePath = '';
        if (is_null($this->aForm)) {
            foreach ($aPages as $oPage) {
                $aAttributes = $this->getDoctrine()->getRepository("NovactiveEzPublishFormGeneratorBundle:Attribute")->findByPage($oPage);
                $this->aForm[$oPage->getId()]["page"] = $oPage;
                foreach ($aAttributes as $oAttribute) {
                    $sFormType = '\Novactive\EzPublishFormGeneratorBundle\Form\\Type\\Generator\\' . ucfirst($oAttribute->getDataTypeString()) . "Type";
                    if (class_exists($sFormType)) {
                        $sAttributeName = "attribute_" . $oAttribute->getDataTypeString() . "_" . $oAttribute->getId();
                        $this->aForm[$oPage->getId()]["my_attributes"][$sAttributeName]["attribute_type"] =
                            array(
                                "key" => $oAttribute->getDataTypeString(),
                                "libelle" => $sFormType::FIELDTYPE_STRING
                            )
                        ;
                        $this->aForm[$oPage->getId(
                        )]["my_attributes"][$sAttributeName]["attribute_id"] = $oAttribute->getId();
                        $this->aForm[$oPage->getId()]["my_attributes"][$sAttributeName]["attribute"] =
                            $this->container->get('form.factory')->create(
                                new $sFormType($oAttribute->getId()),
                                $oAttribute,
                                $options
                            )->createView();
                    }
                }
            }
        }
        $aAttributesType = $this->getAttributeTypes();
        $entity = $this->get('doctrine.orm.entity_manager')->getRepository(
            'NovactiveEzPublishFormGeneratorBundle:Entity'
        )->find($entityId);
        $nbCollections = $this->get('doctrine.orm.entity_manager')->getRepository(
            'NovactiveEzPublishFormGeneratorBundle:Collection')->countByEntity($entity);
        return $this->render(
            "NovactiveEzPublishFormGeneratorBundle:FormGenerator:showEntity.html.twig",
            array(
                "form" => $this->aForm,
                "attributes_type" => $aAttributesType,
                "contentId" => $contentId,
                "nbCollections" => $nbCollections
            )
        );
    }

    /**
     * @param $oPage
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPageAction($oPage)
    {
        $aAttributesType = $this->getAttributeTypes();

        return $this->render(
            "NovactiveEzPublishFormGeneratorBundle:FormGenerator:showPage.html.twig",
            array(
                "page" => $oPage,
                "attributes_type" => $aAttributesType
            )
        );
    }

    protected function getAttributeTypes()
    {
        $aTmpAttributesType = $this->container->getParameter("nova_ezformgenerator.attribute_type");
        $aAttributesType = array();
        foreach ($aTmpAttributesType as $sAttributeType) {
            $sTypeClass = '\Novactive\EzPublishFormGeneratorBundle\Form\\Type\\Generator\\' . ucfirst($sAttributeType) . "Type";
            if (class_exists($sTypeClass)) {
                $aAttributesType[$sAttributeType] = $sTypeClass::FIELDTYPE_STRING;
            }
        }

        return $aAttributesType;
    }

    /**
     * @param $attribute_id
     * @param $attribute_type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAttributeAction($attribute_id, $attribute_type)
    {
        $sFormType = '\Novactive\EzPublishFormGeneratorBundle\\Form\\Type\\Generator\\' . ucfirst($attribute_type) . "Type";
        if (!class_exists($sFormType)) {
            return;
        }
        $oAttribute = $this->getDoctrine()->getRepository("NovactiveEzPublishFormGeneratorBundle:Attribute")->find(
            $attribute_id
        );
        $options = array('csrf_protection' => false);
        $oForm = $this->get("form.factory")->create(new $sFormType($attribute_id), $oAttribute, $options);

        return $this->render(
            "NovactiveEzPublishFormGeneratorBundle:FormGenerator/AttributeType:{$attribute_type}.html.twig",
            array(
                "form" => $oForm->createView(),
                "no_fieldset" => true,
            )
        );
    }

    /**
     * @param $aPost
     * @return bool
     */
    public function createBuilderBack($aPost)
    {
        $aTmpPost = array();

        $options = array('csrf_protection' => false);

        $aKeys = array_keys($aPost);
        foreach ($aKeys as $sKey) {
            $aTmp = explode("_", $sKey);
            if ($aTmp[0] == "attribute") {
                $aTmpPost[$sKey] = $aPost[$sKey];
            }
        }

        $oEntity = $this->getDoctrine()->getRepository("NovactiveEzPublishFormGeneratorBundle:Entity")->find(
            eZSession::get("formentity_id")
        );
        $aPages = $this->getDoctrine()->getRepository("NovactiveEzPublishFormGeneratorBundle:Page")->findBy(
            array('entity' => $oEntity)
        );

        foreach ($aPages as $oPage) {
            $aAttributes = $this->getDoctrine()->getRepository("NovactiveEzPublishFormGeneratorBundle:Attribute")->findByPage($oPage);
            $this->aForm[$oPage->getId()]["page"] = $oPage;
            foreach ($aAttributes as $oAttribute) {
                /**
                 * @var Attribute $oAttribute
                 */
                $sFormType = '\Novactive\EzPublishFormGeneratorBundle\Form\\Type\\Generator\\' . ucfirst(
                        $oAttribute->getDataTypeString()
                    ) . "Type";
                if (!class_exists($sFormType)) {
                    continue;
                }
                $oForm = $this->createForm(new $sFormType($oAttribute->getId()), $oAttribute, $options);
                $sAttributeName = "attribute_" . $oAttribute->getDataTypeString() . "_" . $oAttribute->getId();

                $oForm->setData(array($sAttributeName => $oAttribute));
                $oForm->submit($aTmpPost[$sAttributeName]);
                $this->aForm[$oPage->getId()]["my_attributes"][$sAttributeName]["attribute_type"] = array(
                    "key" => $oAttribute->getDataTypeString(),
                    "libelle" => $sFormType::FIELDTYPE_STRING
                );
                $this->aForm[$oPage->getId()]["my_attributes"][$sAttributeName]["attribute_id"] = $oAttribute->getId();
                $this->aForm[$oPage->getId()]["my_attributes"][$sAttributeName]["attribute"] = $oForm;
                $this->aForm[$oPage->getId()]["my_attributes"][$sAttributeName]["attribute_object"] = $oAttribute;
            }
        }

        $aTmpForm = $this->aForm;
        $isValid = true;
        foreach ($aTmpForm as $iPageId => $aValues) {
            foreach ($aValues["my_attributes"] as $sAttributeName => $aAttribute) {
                $oTmpForm = $aAttribute["attribute"];
                if ($oTmpForm->isValid()) {
                    $oAttribute = $oTmpForm->getData();
                    $oAttribute->setStatus($oAttribute::STATUS_PUBLISHED);
                } else {
                    $isValid = false;
                }
                $this->aForm[$iPageId]["my_attributes"][$sAttributeName]["attribute"] = $oTmpForm->createView();
            }
        }
        $this->getDoctrine()->getManager()->flush();

        return $isValid;
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAttributeListAction($id)
    {
        return $this->render(
            "NovactiveEzPublishFormGeneratorBundle:FormGenerator:showAttributeList.html.twig",
            array(
                "attribute" => "nova_ezformgenerator_subattribute",
                "id" => $id,
                "attribute_types" => $this->getAttributeTypes()
            )
        );
    }

    public function renderIllustrationAction($contentId,$attributeId){
        $content = $this->get('ezpublish.api.repository')->getContentService()->loadContent($contentId);
        return $this->render(
            "NovactiveEzPublishFormGeneratorBundle:FormGenerator:image_line.html.twig",
            array(
                'content' => $content,
                'attributeId' => $attributeId
            )
        );
    }

}