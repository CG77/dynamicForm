<?php


namespace Novactive\EzPublishFormGeneratorBundle\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Novactive\EzPublishFormGeneratorBundle\Entity\Attribute;
use Novactive\EzPublishFormGeneratorBundle\Entity\Collection;
use Novactive\EzPublishFormGeneratorBundle\Entity\CollectionAttributeValue;
use Novactive\EzPublishFormGeneratorBundle\Entity\Entity;
use Novactive\EzPublishFormGeneratorBundle\Entity\Page;
use Novactive\EzPublishFormGeneratorBundle\Entity\SelectionValue;

/**
 * Form manager
 *
 * @author m.monsang <m.monsang@novactive.com>
 */
class FormManager
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Retourne un tableau contenant toutes les réponses du formulaires
     *
     * @param Collection $collection
     * @return array
     */
    public function getAnwsers(Collection $collection)
    {
        $form = $collection->getFormEntity();
        $collectionValueRepository = $this->em->getRepository(
            'NovactiveEzPublishFormGeneratorBundle:CollectionAttributeValue'
        );
        $formDefinition = $form->toArray();

        $values = $collectionValueRepository->findBy(
            array(
                'formCollection' => $collection
            )
        );

        foreach ($formDefinition as &$page) {
            foreach ($page['attributes'] as &$attribute) {
                $this->addAttributeAnswers($attribute, $values);
            }
        }

        return $formDefinition;
    }

    public function getScalarAnswers(Collection $collection)
    {
        $scalarAnswers = array();
        $answers = $this->getAnwsers($collection);

        foreach ($answers as $page) {
            foreach ($page['attributes'] as $attribute) {
                if (!empty($attribute['identifier'])) {
                    $identifier = $attribute['identifier'];
                    if (isset($attribute['answers']) && !empty($attribute['answers'])) {
                        $data = array();

                        foreach ($attribute['answers'] as $tmp) {
                            $data[] = $tmp['value'];
                        }

                        $scalarAnswers[$identifier] = implode(', ', $data);
                    } elseif (!empty($attribute['answer'])) {
                        $scalarAnswers[$identifier] = $attribute['answer'];
                    }
                }
            }
        }

        return $scalarAnswers;
    }

    /**
     * @param array $attributeData
     * @param array $values
     */
    protected function addAttributeAnswers(array &$attributeData, array &$values)
    {
        $attributeId = $attributeData['id'];
        $attributeData['answers'] = array();
        /** @var CollectionAttributeValue $collectionValue */
        foreach ($values as $collectionValue) {
            if ($collectionValue->getFormAttribute()->getId() == $attributeId) {
                if ($collectionValue->getKey()) {
                    $attributeData['answers'][$collectionValue->getKey()] = array(
                        'value' => $collectionValue->getValue(),
                        'key' => $collectionValue->getKey(),
                    );
                } else {
                    $attributeData['answer'] = $collectionValue->getValue();
                }
            }
            if (isset($attributeData['selection_values'])) {
                foreach ($attributeData['selection_values'] as &$selectionValue) {
                    foreach ($selectionValue['sub_attributes'] as &$subAttribute) {
                        $this->addAttributeAnswers($subAttribute, $values);
                    }
                }
            }
        }
    }
} 