<?php

namespace Novactive\EzPublishFormGeneratorBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Content;
use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Novactive\EzPublishFormGeneratorBundle\Entity\Collection;
use Novactive\EzPublishFormGeneratorBundle\Entity\CollectionAttributeValue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;

class FormViewController extends Controller
{

    protected $aForm = null;

    /**
     * @param $collectionId
     * @return \Symfony\Component\HttpFoundation\Response|NotFoundHttpException
     */
    public function showCollectionAction($collectionId)
    {
        $collection = $this->get('doctrine.orm.entity_manager')->getRepository('NovactiveEzPublishFormGeneratorBundle:Collection')->findOneById(
            $collectionId
        );
        if (!$collection) {
            throw new NotFoundHttpException;
        }
        $answers = $this->get('novactive_ezformgenerator.manager.form')->getAnwsers($collection);

        return $this->render(
            'NovactiveEzPublishFormGeneratorBundle:FormView:collection.html.twig',
            array(
                'answers' => $answers,
            )
        );
    }
}