<?php

use Novactive\EzPublishFormGeneratorBundle\Entity\Entity;
use Novactive\EzPublishFormGeneratorBundle\Entity\Page;

class formType extends eZDataType
{

    const DATA_TYPE_STRING = 'form';

    /**
     * Constructor
     *
     */
    function __construct()
    {
        parent::__construct(
            self::DATA_TYPE_STRING,
            ezpI18n::tr('extension/novaformgenerator/datatypes', 'Form'),
            array(
                'serialize_supported' => true,
            )
        );
    }


    /*!
    Returns the content.
   */
    function objectAttributeContent($contentObjectAttribute)
    {
        // get the Container Service
        $serviceContainer = ezpKernel::instance()->getServiceContainer();
        $iUserId = eZUser::currentUserID();

        $oFormController = $serviceContainer->get('novactive_ezformgenerator.generator_controller');
        $em = $serviceContainer->get("doctrine.orm.entity_manager");
        $oParentObject = $contentObjectAttribute->attribute('object');
        if ($contentObjectAttribute->attribute('data_text') != "") {
            $oDoctrine = $serviceContainer->get("doctrine");
            $oEntity = $oDoctrine->getRepository("NovactiveEzPublishFormGeneratorBundle:Entity")->find(
                $contentObjectAttribute->attribute('data_text')
            );
            $oEntity->setName("Formulaire");
            $oEntity->setEzcontentLanguageId($contentObjectAttribute->attribute("language_id"));
            $em->persist($oEntity);

            $aPages = $oDoctrine->getRepository("NovactiveEzPublishFormGeneratorBundle:Page")->findBy(
                array('entity' => $oEntity)
            );
            if ($aPages) {
                $response = $oFormController->showEntityAction($aPages,$oEntity->getId(),$oParentObject->attribute('id'))->getContent();
            } else {
                $oPage = new Page();
                $oPage->setEntity($oEntity);
                $oPage->setDataText("Page 1");
                $em->persist($oPage);
                $em->flush();
                $response = $oFormController->showPageAction($oPage)->getContent();
            }
        } else {
            $oEntity = new Entity();
            $oEntity->setCreatorId($iUserId);

            $oEntity->setName("Formulaire");
            $oEntity->setEzcontentLanguageId($contentObjectAttribute->attribute("language_id"));
            $em->persist($oEntity);

            $oPage = new Page();
            $oPage->setEntity($oEntity);
            $oPage->setDataText("Page 1");
            $em->persist($oPage);
            $em->flush();
            $response = $oFormController->showPageAction($oPage)->getContent();
        }

        $em->flush();

        eZSession::set("formentity_id", $oEntity->getId());
        eZSession::set("language_id", $oEntity->getId($contentObjectAttribute->attribute("language_id")));
        eZSession::set("form_attribute", array());

        $contentObjectAttribute->setAttribute('data_text', $oEntity->getId());
        $contentObjectAttribute->store();

        return $response;
    }

    function validateObjectAttributeHTTPInput($http, $base, $contentObjectAttribute)
    {

        $aPost = $http->attribute("post");

        $serviceContainer = ezpKernel::instance()->getServiceContainer();
        $oFormController = $serviceContainer->get('novactive_ezformgenerator.generator_controller');

        $bIsValid = $oFormController->createBuilderBack($aPost);

        if (!$bIsValid) {
            $contentObjectAttribute->setValidationError("Une erreur s'est produite");

            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Initialize contentobject attribute content
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param integer $currentVersion
     * @param eZContentObjectAttribute $originalContentObjectAttribute
     */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $contentObjectID = $contentObjectAttribute->attribute( 'contentobject_id' );
            $originalContentObjectID = $originalContentObjectAttribute->attribute( 'contentobject_id' );

            // Case when content object was copied or when new translation has been added to the existing one
            if ( ( $contentObjectID != $originalContentObjectID ) ){
                $originalObject = eZContentObject::fetch( $originalContentObjectID );
                $object = eZContentObject::fetch( $contentObjectID );




                $versions = $object->versions();

                $firstVersion = $versions[0]->attribute('version');


                $oldEntityID = $versions[0]->dataMap()['form']->attribute('data_text');
                $serviceContainer = ezpKernel::instance()->getServiceContainer();
                $em = $serviceContainer->get("doctrine.orm.entity_manager");
                $entity = $em->getRepository('NovactiveEzPublishFormGeneratorBundle:Entity')->find($oldEntityID);
                $copy = clone $entity;
                if($firstVersion === $currentVersion){
                    $em->persist($copy);
                }
                $em->flush();
                $oldName = $originalObject->dataMap()['lib']->attribute('data_text');
                $oName = $versions[0]->dataMap()['lib'];
                $oName->setAttribute('data_text','copie de '.$oldName);
                $oName->setAttribute('version',$originalObject->attribute('current_version'));
                $oName->store();
                $contentObjectAttribute->setAttribute('data_text',$copy->getId());
                $contentObjectAttribute->store();
            }
        }
    }

}

eZDataType::register(formType::DATA_TYPE_STRING, "formType");

?>
