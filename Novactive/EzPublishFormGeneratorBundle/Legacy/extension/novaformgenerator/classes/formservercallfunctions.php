<?php

use Novactive\EzPublishFormGeneratorBundle\Entity\Page;
use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Novactive\EzPublishFormGeneratorBundle\Entity\ContentAttributeValue;
use Novactive\EzPublishFormGeneratorBundle\Entity\SelectionValue;

use Doctrine\Common\Collections\ArrayCollection;


class formServerCallFunctions extends ezjscServerFunctions {

    public static function addPage( ) {
        $http = eZHTTPTool::instance();
        $serviceContainer = ezpKernel::instance()->getServiceContainer();
        $iEntityId = eZSession::get( "formentity_id" );
        $oDoctrine = $serviceContainer->get( "doctrine" );
        $oEntity = $oDoctrine->getRepository( "NovactiveEzPublishFormGeneratorBundle:Entity" )->find( $iEntityId );

        if ( $http->hasPostVariable( "nbPage" ) ) {
            $em = $serviceContainer->get( "doctrine.orm.entity_manager" );
            $oPage = new Page();
            $oPage->setEntity( $oEntity );
            $iNbPage = $http->postVariable( "nbPage" ) + 1;
            $oPage->setDataText( "Page " . $iNbPage );
            $em->persist( $oPage );
            $em->flush();

            $oFormController = $serviceContainer->get( 'novactive_ezformgenerator.generator_controller' );
            return $oFormController->showPageAction( $oPage )->getContent();
        }
    }

    public static function changePageName( ) {
        $http = eZHTTPTool::instance();
        $serviceContainer = ezpKernel::instance()->getServiceContainer();
        $oDoctrine = $serviceContainer->get( "doctrine" );
        $em = $serviceContainer->get( "doctrine.orm.entity_manager" );

        $oPage = $oDoctrine->getRepository( "NovactiveEzPublishFormGeneratorBundle:Page" )->find( $http->postVariable( "id" ) );
        $oPage->setDataText( $http->postVariable( "value" ) );
        $em->persist( $oPage );
        $em->flush();
        return $http->postVariable( "value" );
    }

    public static function deletePage( ) {
        $http = eZHTTPTool::instance();
        $serviceContainer = ezpKernel::instance()->getServiceContainer();
        $oDoctrine = $serviceContainer->get( "doctrine" );
        $em = $serviceContainer->get( "doctrine.orm.entity_manager" );

        $oPage = $oDoctrine->getRepository( "NovactiveEzPublishFormGeneratorBundle:Page" )->find( $http->postVariable( "id" ) );
        $em->remove( $oPage );
        $em->flush();
        return true;
    }

    public static function addAttribute( ) {
        $http = eZHTTPTool::instance();
        $serviceContainer = ezpKernel::instance()->getServiceContainer();
        $oDoctrine = $serviceContainer->get( "doctrine" );
        $em = $serviceContainer->get( "doctrine.orm.entity_manager" );
        $oFormController = $serviceContainer->get( 'novactive_ezformgenerator.generator_controller' );

        $oAttribute = new Attribute();

        if( $http->hasPostVariable( "pageid" ) ) {
            $iEntityId = eZSession::get( "formentity_id" );
            $oEntity = $oDoctrine->getRepository( "NovactiveEzPublishFormGeneratorBundle:Entity" )->find( $iEntityId );
            $oPage = $oDoctrine->getRepository( "NovactiveEzPublishFormGeneratorBundle:Page" )->find( $http->postVariable( "pageid" ) );
            $oAttribute->setEntity( $oEntity );
            $oAttribute->setPage( $oPage );
            $oAttribute->setPlacement($http->postVariable('placement'));
        }

        $oAttribute->setStatus( $oAttribute::STATUS_DRAFT );
        $oAttribute->setDataTypeString( $http->postVariable( "attribute" ) );
        $oAttribute->setEzcontentLanguageId( eZSession::get( "language_id" ) );
        $em->persist( $oAttribute );

        if ( $http->postVariable( "attribute" ) == "selection" ) {
            $oSelection = new SelectionValue();
            $oSelection->setFormAttribute( $oAttribute );
            $em->persist( $oSelection );
        }

        if ( $http->hasPostVariable( "selectionValue_id" ) ) {
            $oSelection = $oDoctrine->getRepository( "NovactiveEzPublishFormGeneratorBundle:SelectionValue" )->find( $http->postVariable( "selectionValue_id" ) );

            $collection = $oSelection->getSubAttributes();
            $collection->add($oAttribute);

            $oSelection->setSubAttributes($collection);
            $em->persist( $oSelection );

        }

        $em->flush();

        return $oFormController->showAttributeAction( $oAttribute->getId(), $http->postVariable( "attribute" ) )->getContent();
    }

    public static function deleteAttribute( $args ) {
        $http = eZHTTPTool::instance();
        $serviceContainer = ezpKernel::instance()->getServiceContainer();
        $oDoctrine = $serviceContainer->get( "doctrine" );
        $em = $serviceContainer->get( "doctrine.orm.entity_manager" );

        $oAttribute = $oDoctrine->getRepository( "NovactiveEzPublishFormGeneratorBundle:Attribute" )->find( $http->postVariable( "id" ) );
        $em->remove( $oAttribute );

        $em->flush();
        return true;
    }

    public static function showAttributeList() {
        $http = eZHTTPTool::instance();
        $serviceContainer = ezpKernel::instance()->getServiceContainer();
        $oFormController = $serviceContainer->get( 'novactive_ezformgenerator.generator_controller' );
        return $oFormController->showAttributeListAction( $http->postVariable( "id" ) )->getContent();
    }

} 