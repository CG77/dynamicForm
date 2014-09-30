<?php

$tpl = eZTemplate::factory();
$Module = $Params['Module'];
$http = eZHTTPTool::instance();
$aResult = array();
$success = true;
$objectName = '';
$customIni       = eZINI::instance( 'custom.ini' );
$acceptedFiles = array('jpg','jpeg','gif','png','bmp');
$aUserParams = $Params['UserParameters'];

if($http->hasPostVariable('btnSubmitImage')){
    $file = $_FILES['fileName'];
    $aResult['success'] = false;
    if ( $http->postVariable( 'objectName' ) === ''){
        $aResult['error']['name'] = 'Ce champ est requis';
        $success = false;
    }
    else{
        $objectName = trim( $http->postVariable( 'objectName' ) );
    }

    if($file['error'] === 4){
        $aResult['error']['file'] = 'Ce champ est requis';
        $success = false;
    }
    if(!empty($file['name'])){
        $aName = explode('.',$file['name']);
        if( !in_array($aName[sizeof($aName) - 1],$acceptedFiles) ){
            $aResult['error']['file'] = "Sélectionner une image";
            $aResult['error']['generic_file'] = "Nous n'avons pas pu enregistrer votre image, veuillez la vérifier.";
            $success = false;
        }
    }

    if($success === true){

        $objectID        = (int) $Params['ObjectID'];
        $objectVersion   = (int) $Params['ObjectVersion'];
        $location    = (int) $customIni->variable( 'ImageCreationSettings', 'container' );

        $object    = eZContentObject::fetch( $objectID );
        $version   = eZContentObjectVersion::fetchVersion( $objectVersion, $objectID );
        if ( !$version )
        {
            echo  "Paramètre non valide. ";
            eZExecution::cleanExit();
        }

        $upload = new eZContentUpload();

        $uploadedOk = $upload->handleUpload(
            $result,
            'fileName',
            $location,
            false,
            $objectName,
            $version->attribute( 'initial_language' )->attribute( 'locale' )
        );

        if ( $uploadedOk )
        {
            $newObject = $result['contentobject'];
            $newObjectID = $newObject->attribute( 'id' );
            $newObjectName = $newObject->attribute( 'name' );
            $newObjectNodeID = (int) $newObject->attribute( 'main_node_id' ); // this will be empty if object is stopped by approve workflow

            // set parent section for new object
            if ( isset( $newObjectNodeID ) && $newObjectNodeID )
            {
                $newObjectParentNodeObject = $newObject->attribute( 'main_node' )->attribute( 'parent' )->attribute( 'object' );
                $newObject->setAttribute( 'section_id', $newObjectParentNodeObject->attribute( 'section_id' ) );
                $newObject->store();

                $dataMap = $newObject->dataMap();
                $imgAlias = $dataMap['image']->content()->aliasList();
                $aResult['success'] = "Image créée avec succès";
                $aResult['objectId'] = $newObjectID;
                $aResult['image'] = $imgAlias['original']['url'];
                $aResult['name'] = $dataMap['name']->attribute('data_text');
            }
        }
    }
    echo json_encode($aResult);
    eZExecution::cleanExit();
}
if($aUserParams['image'] != 0){
    $imageObject = eZContentObject::fetch( (int) $aUserParams['image'] );
    $dataMap = $imageObject->dataMap();
    $imgAlias = $dataMap['image']->content()->aliasList();
    $tpl->setVariable('name',$dataMap['name']->attribute('data_text'));
    $tpl->setVariable('image',$imgAlias['original']['url']);
    $tpl->setVariable('objectId',$aUserParams['image']);
}
$Result =array();
$Result['content'] = $tpl->fetch('design:image/illustration.tpl');
$Result['pagelayout'] = 'design:popup_pagelayout.tpl';

return $Result;

?>
