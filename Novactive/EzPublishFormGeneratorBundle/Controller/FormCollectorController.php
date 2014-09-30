<?php

namespace Novactive\EzPublishFormGeneratorBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Content;
use Monolog\Handler\StreamHandler;
use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Novactive\EzPublishFormGeneratorBundle\Entity\Collection;
use Novactive\EzPublishFormGeneratorBundle\Entity\CollectionAttributeValue;
use Novactive\EzPublishFormGeneratorBundle\Entity\Entity;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class FormCollectorController extends Controller
{

    protected $aForm = null;

    public function viewLocationAction($locationId, $viewType , $layout = false, array $params = array()){

        $location = $this->get('ezpublish.api.repository')->getLocationService()->loadLocation($locationId);

        $content = $this->get('ezpublish.api.repository')->getContentService()->loadContentByContentInfo($location->getContentInfo());


        $form = $this->showFormAction($content->getVersionInfo()->getContentInfo()->id);

        $hash_bundle_name = $this->container->getParameter('hash_bundle_name');

        $template = $hash_bundle_name[$this->getRequest()->attributes->get('siteaccess')->name].':Form:'.$viewType.'.html.twig';

        return $this->render( $template, array(
                'location' => $location,
                'content'  => $content,
                'form' =>  $form
            )
        );
    }

    /**
     * @param $contentId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFormAction( $contentId ) {
        $page = 1;
        $content = $this->get('ezpublish.api.repository')->getContentService()->loadContent($contentId);
        $entityId = $content->getFieldValue('form')->text;
        if (!$entityId) {
            return new NotFoundHttpException();
        }
        $entity = $this->get('doctrine.orm.entity_manager')->getRepository(
            'NovactiveEzPublishFormGeneratorBundle:Entity'
        )->find($entityId);
        $countPages = $this->get('doctrine.orm.entity_manager')->getRepository(
            'NovactiveEzPublishFormGeneratorBundle:Page')->countByEntity($entity);
        if (!$entity) {
            return new NotFoundHttpException();
        }
        $attachments = array();
        $form = $this->createForm('form_collector_builder', array(), array(
            'entity' => $entity,
            'captcha' => $content->getFieldValue('is_captcha')->bool,
            'is_mds' => $content->getFieldValue('is_mds')->bool,
            'countPages' => $countPages,
        ));
        if ($this->get('request')->isMethod('post')) {
            $form->handleRequest($this->get('request'));
            if ($form->isValid()) {
                $data = $form->getData();
                $collection = new Collection();
                $collection->setFormEntity($entity);
                $collection->setCreated(new \DateTime());
                foreach ($data as $key => $page) {
                    foreach ($page as $attributeId => $collectionAttributeValue) {
                        $this->addAttributeValue($collection, $collectionAttributeValue, $attributeId, $attachments, $this->getRequest()->request->all()['form_collector_builder']['is_mds'] );
                    }
                }

                $this->get('doctrine.orm.entity_manager')->persist($collection);
                $this->get('doctrine.orm.entity_manager')->flush();
                if( $content->getFieldValue('template_pdf')->uri ){
                    //Génération de pdf
                    $results = array();
                    $answers = $this->getAllAnswers($this->get('novactive_ezformgenerator.manager.form')->getAnwsers($collection),
                        'attributes',$results);
                    $fileName = $this->generatePdf($answers,$content->getFieldValue('template_pdf')->uri,$contentId,$content->getVersionInfo()->getContentInfo()->remoteId);
                    $collection->setGeneratedFile($fileName);
                    //Fin génération de pdf
                }

                $this->notifyValidator($content, $collection, $attachments, $this->getRequest()->request->all()['form_collector_builder']['is_mds']);
                $this->notifyUser($content, $collection, $attachments);
                $url = $this->getRedirectUrl($content, "success_link");
                if ($url) {
                    return $this->redirect($url);
                }else{
                    $location = $this->get('ezpublish.api.repository')->getLocationService()->loadLocation(
                        (int) $this->container->getParameter('nova_ezformgenerator.default.link_success')
                    );
                    return $this->redirect($this->generateUrl($location));
                }
                $this->get('session')->getFlashBag()->add('form.error', 'Formulaire enregistré !');
            } else {
                $page = $countPages;
                if($form->getErrors()){
                    $this->get('session')->getFlashBag()->add(
                        'form.error',
                        'Attention, nous avons relevé des erreurs dans le formulaire'
                    );
                }
                if($content->getFieldValue('is_captcha')->bool and $form->get('captcha')->getErrors()){
                    $this->get('session')->getFlashBag()->add(
                        'form.error',
                        'Merci de vérifier votre saisie dans le Captcha de contrôle'
                    );
                }elseif(!$form->getErrors()){
                    $url = $this->getRedirectUrl($content, "fail_link");
                    if ($url) {
                        return $this->redirect($url);
                    }else{
                        $location = $this->get('ezpublish.api.repository')->getLocationService()->loadLocation(
                            (int) $this->container->getParameter('nova_ezformgenerator.default.error_link')
                        );
                        return $this->redirect($this->generateUrl($location));
                    }
                    $this->get('session')->getFlashBag()->add(
                        'form.error',
                        'Une erreur est survenue pendant la validation du formulaire.'
                    );
                }
            }
        }
        return $this->render(
            'NovactiveEzPublishFormGeneratorBundle:FormCollector:form.html.twig',
            array(
                'form' => $form->createView(),
                'entity' => $entity,
                'page' => $page,
                'content' => $content
            )
        );
    }
    /**
     * @param Collection $collection
     * @param $collectionAttributeValue
     * @param $attributeId
     */
    protected function addAttributeValue(
        Collection $collection,
        $collectionAttributeValue,
        $attributeId,
        array &$attachments,
        $mds
    ) {
        $attributeRepository = $this->get('doctrine.orm.entity_manager')->getRepository(
            'NovactiveEzPublishFormGeneratorBundle:Attribute'
        );
        $attribute = $attributeRepository->find($attributeId);
        if($mds && in_array($attribute->getDataTypeString(),array('text','integer')) &&
            $attribute->getIdentifier() == 'code_postal'){
            $collection->setZipCodeMds($collectionAttributeValue->getValue());
        }
        $constraints = $attribute->getConstraints();
        if ($collectionAttributeValue instanceof CollectionAttributeValue) {
            $collectionAttributeValue->setFormCollection($collection);
            /** @var Attribute $attribute */
            $collectionAttributeValue->setFormAttribute($attribute);
            if ($collectionAttributeValue->getValue() instanceof \DateTime) {
                $collectionAttributeValue->setValue($collectionAttributeValue->getValue()->format('Y-m-d'));
            }
            $collection->getCollectionAttributeValue()->add($collectionAttributeValue);

            if ($attribute->getDataTypeString() == 'file') {

                $file = $this->get('novactive_ezformgenerator.store.file')->store($collectionAttributeValue->getValue(),(string)$attribute->getId());
                $collectionAttributeValue->setValue(serialize($file));

                if (!empty($constraints['join_as_attachment'])) {
                    $attachments[] = $this->get('novactive_ezformgenerator.store.file')->getFileInfos(
                        unserialize($collectionAttributeValue->getValue())
                    );
                }
            }
        } elseif (is_array($collectionAttributeValue) && array_key_exists('selection', $collectionAttributeValue)) {
            // Gestion des sélections multiples
            $value = $collectionAttributeValue['selection'];
            if (!is_array($value)) {
                $value = array($value);
            }
            foreach ($value as $key => $text) {
                $collectionValue = new CollectionAttributeValue();
                $collectionValue->setKey($text);
                $collectionValue->setValue($attribute->getLabelForKey($text));
                $collectionValue->setFormAttribute($attribute);
                $collectionValue->setFormCollection($collection);
                $collection->getCollectionAttributeValue()->add($collectionValue);
            }
        } elseif (is_array($collectionAttributeValue) && array_key_exists('grid', $collectionAttributeValue)) {
            // Gestion du type matrice
            foreach ($collectionAttributeValue['grid'] as $key => $collectionValue) {
                $collectionValue->setFormAttribute($attribute);
                $collectionValue->setFormCollection($collection);
                $collectionValue->setKey($key);
                $collection->getCollectionAttributeValue()->add($collectionValue);
            }
        }

        // Gestion des sous questions (fonction récursive)
        if (is_array($collectionAttributeValue) && array_key_exists('subAttributes', $collectionAttributeValue)) {
            foreach ($collectionAttributeValue['subAttributes'] as $panel => $panelContent) {
                foreach ($panelContent as $attributeId => $collectionAttributeValue) {
                    $this->addAttributeValue($collection, $collectionAttributeValue, $attributeId, $attachments,$mds);
                }
            }
        }
    }

    /**
     * @param Content $content
     * @param $attribute
     * @return null|string
     */
    protected function getRedirectUrl(Content $content, $attribute)
    {
        $urlSuccess = $content->getFieldValue($attribute);
        if ($urlSuccess && !empty($urlSuccess->destinationContentId)) {
            $destinationContentId = $urlSuccess->destinationContentId;
            try {
                $content = $this->get('ezpublish.api.repository')->getContentService()->loadContent(
                    $destinationContentId
                );
                $location = $this->get('ezpublish.api.repository')->getLocationService()->loadLocation(
                    $content->contentInfo->mainLocationId
                );

                return $this->generateUrl($location);
            } catch (NotFoundException $ex) {
                $this->get('logger')->err(
                    sprintf("Content with id [%d] not found : %s", $destinationContentId, $ex->getMessage())
                );
            }
        }

        return null;
    }


    /**
     * @param Content $content
     * @param Collection $collection
     * @param array $attachments
     * @param boolean $mds
     */
    protected function notifyValidator( Content $content, Collection $collection, array $attachments, $mds )
    {
        $to = $cc = $bcc = null;
        if ($content->getFieldValue('validator_subject')) {
            $title = $content->contentInfo->name;
            $subject = $content->getFieldValue('validator_subject')->text;

            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setContentType('text/html');

            $from = null;
            if ($content->getFieldValue('validator_sender_email')) {
                $from = $content->getFieldValue('validator_sender_email')->text;
                if ($from) {
                    $message->setFrom($from);
                }
            }
            if (!$from) {
                $message->setFrom($this->container->getParameter('novactive_ez_publish_form.default_from_email'));
            }

            if ($content->getFieldValue('validator_email')) {
                $to = $content->getFieldValue('validator_email')->text;


            }
            if ($content->getFieldValue('validator_cc_email')) {
                $cc = $content->getFieldValue('validator_cc_email')->text;
            }
            if ($content->getFieldValue('validator_bcc_email')) {
                $bcc = $content->getFieldValue('validator_bcc_email')->text;
            }
            if (!$to && !$cc && !$bcc) {
                return;
            }

            if($mds){
                $zip =  $collection->getZipCodeMds();

                if(!empty($zip)){
                    $mails = $this->get('novactive_ezformgenerator.store.file')->getMailsFromCsv(
                        $collection->getZipCodeMds($zip));
                    if($mails != false){
                        $tabMdsMails = explode('|',$mails);
                        foreach($tabMdsMails as $email){
                            $message->addTo($email);
                        }
                    }
                }
            }

            if ($to) {
                $tab = explode(',', $to);
                foreach ($tab as $email) {
                    $message->addTo($email);
                }

            }
            if ($cc) {
                $tab = explode(',', $cc);
                foreach ($tab as $email) {
                    $message->addCc($email);
                }
            }
            if ($bcc) {
                $tab = explode(',', $cc);
                foreach ($tab as $email) {
                    $message->addBcc($email);
                }
            }

            foreach ($attachments as $attachment) {
                $message->attach(\Swift_Attachment::fromPath($attachment['path'])->setFilename($attachment['name']));
            }
            $fileName = $collection->getGeneratedFile();
            if(!empty($fileName)){
                $message->attach(\Swift_Attachment::fromPath($fileName)->setFilename($content->getFieldValue('lib')->text));
            }
            try {
                $body = $this->get('templating')->render(
                    "NovactiveEzPublishFormGeneratorBundle:Email:validator.html.twig",
                    array('collection' => $collection, 'title' => $title)
                );
                $message->setBody($body);
                $this->get('mailer')->send($message);
            } catch (\Exception $ex) {
                // catch
                $this->get('logger')->error($ex->getMessage());
                throw $ex;
            }
        }
    }

    /**
     * @param Content $content
     * @param Collection $collection
     * @param array $attachments
     */
    protected function notifyUser(Content $content, Collection $collection, array $attachments)
    {
        $to = $cc = $bcc = null;
        if ($content->getFieldValue('user_subject_email') && $content->getFieldValue('user_body_email')) {
            $scalarAnswers = $this->get('novactive_ezformgenerator.manager.form')->getScalarAnswers($collection);


            $subject = $content->getFieldValue('user_subject_email')->text;
            $body = $content->getFieldValue('user_body_email')->text;
            try {
                $subject = $this->replaceVariables($scalarAnswers, $subject);
                $body = $this->replaceVariables($scalarAnswers, nl2br( $body ));
                if (!$subject || !$body) {
                    return;
                }
                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setContentType('text/html');

                if ($content->getFieldValue('user_sender_email')) {
                    $from = $content->getFieldValue('user_sender_email')->text;
                    if ($from) {
                        $from = $this->replaceVariables($scalarAnswers, $from);
                        if ($from) {
                            $message->setFrom($from);
                        }
                    } else {
                        $message->setFrom($this->container->getParameter('novactive_ez_publish_form.default_from_email'));
                    }
                }
                if ($content->getFieldValue('user_to_email')) {
                    $to = $content->getFieldValue('user_to_email')->text;
                    $to = $this->replaceVariables($scalarAnswers, $to);
                }
                if (!$to) {
                    return;
                }
                $message->setTo($to);
                $message->setBody($body);
                $this->get('mailer')->send($message);
            } catch (\Exception $ex) {
                // catch
                $this->get('logger')->error($ex->getMessage());
            }
        }
    }

    /**
     * @param $answers
     * @param $string
     * @return mixed
     */
    protected function replaceVariables($answers, $string)
    {
        $search = array();
        $replace = array();

        foreach ($answers as $key => $value) {
            $search[] = '$' . $key . '$';
            $replace[] = $value;
        }
        return str_replace($search, $replace, $string);
    }

    /**
     * Log post data
     *
     * @param Request $request
     * @param Entity $entity
     */
    protected function log(Request $request, Entity $entity)
    {
        $path = $this->container->getParameter('kernel.logs_dir') . '/form.log';
        $handler = new StreamHandler($path, LogLevel::INFO);
        $now = new \DateTime();
        $user = $this->get('ezpublish.api.repository')->getCurrentUser();
        if ($user) {
            $user = $user->id . '-' . $user->email;
        }

        $this->get('logger')->pushHandler($handler);
        $this->get('logger')->info(
            sprintf(
                '[%s][EntityId=%s][User=%s] %s',
                $now->format('c'),
                $entity->getId(),
                $user,
                var_export($request, 1)
            )
        );
        $this->get('logger')->popHandler();
    }

    public function displayIllustrationAction($contentId){
        $content = $this->get('ezpublish.api.repository')->getContentService()->loadContent($contentId);
        return $this->render(
            "NovactiveEzPublishFormGeneratorBundle:FormCollector:image_line.html.twig",
            array(
                'content' => $content,
            )
        );
    }

    public function getAllAnswers($answers, $index, array &$results ){
        if(empty($results))
            $results = array();
        foreach ($answers as $answer){
            foreach($answer[$index] as $attribute){
                if(isset($attribute['answer']) && !empty($attribute['answer'])){
                    $results[$attribute['identifier']] = $attribute['answer'];
                }
                if(isset($attribute['answers']) && !empty($attribute['answers'])){
                    foreach($attribute['answers'] as $key=>$reponse){
                        $results[$key] = $reponse['value'];
                    }
                }
                if(isset($attribute['selection_values']) and !empty($attribute['selection_values'])){
                    $this->getAllAnswers($attribute['selection_values'],'sub_attributes',$results);
                }
            }
        }
        return $results;
    }
    /*
     * @param $answers ArrayCollection
     * @param $uri string
     * @param $contentId integer
     * @param $remoteId string
     * @return string
     */
    public function generatePdf($answers,$uri,$contentId,$remoteId){
        $date = new \DateTime();
        $prefix = $contentId.$remoteId.'_'.$date->getTimestamp();
        return $this->get('novactive_ezformgenerator.store.file')->generatePdf($answers,$uri,$prefix,'pdf');
    }

    public function getCommuneAction(){
        if($this->getRequest()->isMethod('POST')){
            $value = $this->get('request')->request->get('value');
            return new JsonResponse($this->get('novactive_ezformgenerator.store.file')->getCommuneFromCsv($value));
        }

    }
}