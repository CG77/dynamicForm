<?php

namespace Novactive\EzPublishFormGeneratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Attribute
 *
 * @ORM\Table(name="TBL_attribute", indexes={@ORM\Index(name="Entity1ID", columns={"ENTITY_id"}), @ORM\Index(name="PageID_idx", columns={"PAGE_id"})})
 * @ORM\Entity(repositoryClass="Novactive\EzPublishFormGeneratorBundle\Repository\AttributeRepository")
 */
class Attribute
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="data_type_string", type="string", length=45, nullable=false)
     */
    private $dataTypeString;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=45, nullable=true)
     */
    private $identifier;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_collector", type="boolean", nullable=true)
     */
    private $isCollector = true;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_required", type="boolean", nullable=true)
     */
    private $isRequired;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="placement", type="integer", nullable=true)
     */
    private $placement;

    /**
     * @var integer
     *
     * @ORM\Column(name="illustration_object_id", type="integer", nullable=true)
     */
    private $illustrationObjectId;

    /**
     * @var string
     *
     * @ORM\Column(name="constraints", type="array", nullable=true)
     */
    private $constraints;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var integer
     *
     * @ORM\Column(name="EZCONTENT_LANGUAGE_id", type="integer", nullable=true)
     */
    private $ezcontentLanguageId;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = self::STATUS_DRAFT;

    /**
     * @var Page
     *
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="attributes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PAGE_id", referencedColumnName="id", onDelete="cascade")
     * })
     */
    private $page;

    /**
     * @var array
     *
     * @ORM\Column(name="data_text", type="array", nullable=true)
     */
    private $dataText = array();

    /**
     * @var \Entity
     *
     * @ORM\ManyToOne(targetEntity="Entity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ENTITY_id", referencedColumnName="id")
     * })
     */
    private $entity;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SelectionValue", mappedBy="formAttribute", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"placement" = "ASC"})
     */
    private $selectionValues;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="SelectionValue", mappedBy="subAttributes", cascade={"persist"})
     */
    private $selectionAttributes;

    public function __construct()
    {
        $this->selectionValues = new ArrayCollection();
    }

    /**
     * @param mixed $selectionAttributes
     */
    public function setSelectionAttributes($selectionAttributes)
    {

        $this->selectionAttributes = $selectionAttributes;
    }

    /**
     * @return mixed
     */
    public function getSelectionAttributes()
    {
        return $this->selectionAttributes;
    }

    /**
     * @param Collection $selectionValues
     * @return $this
     */
    public function setSelectionValues($selectionValues)
    {

        $aOld = array();
        $aNew = array();

        /** @var SelectionValue $selectionValue */
        foreach ($this->selectionValues as $selectionValue) {
            $aOld[$selectionValue->getId()] = $selectionValue;
        }

        foreach ($selectionValues as $selectionValue) {
            if ($selectionValue->getId() && array_key_exists($selectionValue->getId(), $aOld)) {
                unset($aOld[$selectionValue->getId()]);
                continue;
            }

            $selectionValue->setFormAttribute($this);
            $aNew[] = $selectionValue;
        }

        foreach ($aOld as $selectionValue) {
            $selectionValue->unsetFormAttribute();
            $this->selectionValues->removeElement($selectionValue);
        }


        foreach ($aNew as $selectionValue) {
            $this->selectionValues->add($selectionValue);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSelectionValues()
    {
        return $this->selectionValues;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dataTypeString
     *
     * @param string $dataTypeString
     * @return Attribute
     */
    public function setDataTypeString($dataTypeString)
    {
        $this->dataTypeString = $dataTypeString;

        return $this;
    }

    /**
     * Get dataTypeString
     *
     * @return string
     */
    public function getDataTypeString()
    {
        return $this->dataTypeString;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Attribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return Attribute
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set isCollector
     *
     * @param boolean $isCollector
     * @return Attribute
     */
    public function setIsCollector($isCollector)
    {
        $this->isCollector = $isCollector;

        return $this;
    }

    /**
     * Get isCollector
     *
     * @return boolean
     */
    public function getIsCollector()
    {
        return $this->isCollector;
    }

    /**
     * Set isRequired
     *
     * @param boolean $isRequired
     * @return Attribute
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get isRequired
     *
     * @return boolean
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Attribute
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set placement
     *
     * @param integer $placement
     * @return Attribute
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * Get placement
     *
     * @return integer
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * Set illustrationObjectId
     *
     * @param integer $illustrationObjectId
     * @return Attribute
     */
    public function setIllustrationObjectId($illustrationObjectId)
    {
        $this->illustrationObjectId = $illustrationObjectId;

        return $this;
    }

    /**
     * Get illustrationObjectId
     *
     * @return integer
     */
    public function getIllustrationObjectId()
    {
        return $this->illustrationObjectId;
    }

    /**
     * Set constraints
     *
     * @param string $constraints
     * @return Attribute
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * Get constraints
     *
     * @return string
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Attribute
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Attribute
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set ezcontentLanguageId
     *
     * @param integer $ezcontentLanguageId
     * @return Attribute
     */
    public function setEzcontentLanguageId($ezcontentLanguageId)
    {
        $this->ezcontentLanguageId = $ezcontentLanguageId;

        return $this;
    }

    /**
     * Get ezcontentLanguageId
     *
     * @return integer
     */
    public function getEzcontentLanguageId()
    {
        return $this->ezcontentLanguageId;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Attribute
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set page
     *
     * @param \Novactive\EzPublishFormGeneratorBundle\Entity\Page $page
     * @return Attribute
     */
    public function setPage(\Novactive\EzPublishFormGeneratorBundle\Entity\Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Novactive\EzPublishFormGeneratorBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set entity
     *r
     * @param \Novactive\EzPublishFormGeneratorBundle\Entity\Entity $entity
     * @return Attribute
     */
    public function setEntity(\Novactive\EzPublishFormGeneratorBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \Novactive\EzPublishFormGeneratorBundle\Entity\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param array $dataText
     */
    public function setDataText($dataText)
    {
        /**
         * Réindexation des colonnes et des lignes
         * En front, lorsque l'on ajoute une colonne, on lui donne comme index le nombre total de colonnes
         * Ce qui peut provoquer des conflits
         */
        if (!empty($dataText['row']) && is_array($dataText['row'])) {
            $dataText['row'] = array_values($dataText['row']);
        }
        if (!empty($dataText['column']) && is_array($dataText['column'])) {
            $dataText['column'] = array_values($dataText['column']);
        }
        $this->dataText = $dataText;
    }

    /**
     * @return array
     */
    public function getDataText()
    {
        return $this->dataText;
    }

    /**
     * @param $key
     * @return null|string
     */
    public function getLabelForKey($key)
    {
        /** @var SelectionValue $selectionValue */
        foreach ($this->getSelectionValues() as $selectionValue) {
            if ($selectionValue->getValue() == $key) {
                return $selectionValue->getLibelle();
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $tab = array(
            'label' => $this->getName(),
            'id' => $this->getId(),
            'data_type' => $this->getDataTypeString(),
            'selection_values' => array(),
            'constraints' => $this->getConstraints(),
            'data_text' => $this->getDataText(),
            'identifier' => $this->getIdentifier(),
        );

        /** @var SelectionValue $selectionValue */
        foreach ($this->getSelectionValues() as $selectionValue) {
            $tab['selection_values'][$selectionValue->getValue()] = $selectionValue->toArray();
        }
        return $tab;
    }

    public function __clone() {
        $selectionValues = $this->getSelectionValues();
        $this->selectionValues = new ArrayCollection();
        if(!empty($selectionValues)){
            foreach ($selectionValues as $selectionValue) {
                $newSelectionValue = clone $selectionValue;
                $this->selectionValues->add($newSelectionValue);
                $newSelectionValue->setFormAttribute($this);
                $subAttributes = $newSelectionValue->getSubAttributes();
                $aSubAttributes = new ArrayCollection();
                foreach($subAttributes as $subAttribute){
                    $newSubAttribute = clone $subAttribute;
                    $aSubAttributes->add($newSubAttribute);
                }
                $newSelectionValue->setSubAttributes($aSubAttributes);
            }
        }

    }
}
