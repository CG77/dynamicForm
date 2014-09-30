<?php

namespace Novactive\EzPublishFormGeneratorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Collection
 *
 * @ORM\Table(name="TBL_collection", indexes={@ORM\Index(name="formEntityID_idx", columns={"FORM_ENTITY_id"})})
 * @ORM\Entity(repositoryClass="Novactive\EzPublishFormGeneratorBundle\Repository\CollectionRepository")
 */
class Collection
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var integer
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=true)
     */
    private $creatorId;

    /**
     * @var Entity
     *
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="formEntity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FORM_ENTITY_id", referencedColumnName="id")
     * })
     */
    private $formEntity;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CollectionAttributeValue", mappedBy="formCollection", cascade={"all"}, orphanRemoval=true)
     */
    private $collectionAttributeValue;

    /**
     * @var string
     */
    private $generatedFile;

    private $zipCodeMds;

    public function __construct()
    {
        $this->collectionAttributeValue = new ArrayCollection();
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
     * Set created
     *
     * @param \DateTime $created
     * @return Collection
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
     * Set creatorId
     *
     * @param integer $creatorId
     * @return Collection
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    /**
     * Get creatorId
     *
     * @return integer
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * Set formEntity
     *
     * @param Entity $formEntity
     * @return $this
     */
    public function setFormEntity(Entity $formEntity = null)
    {
        $this->formEntity = $formEntity;

        return $this;
    }

    /**
     * Get formEntity
     *
     * @return Entity
     */
    public function getFormEntity()
    {
        return $this->formEntity;
    }

    /**
     * @param ArrayCollection $collectionAttributeValue
     */
    public function setCollectionAttributeValue($collectionAttributeValue)
    {
        $this->collectionAttributeValue = $collectionAttributeValue;
    }

    /**
     * @return ArrayCollection
     */
    public function getCollectionAttributeValue()
    {
        return $this->collectionAttributeValue;
    }

    /**
     * @param string $generatedFile
     */
    public function setGeneratedFile($generatedFile)
    {
        $this->generatedFile = $generatedFile;
    }

    /**
     * @return string
     */
    public function getGeneratedFile()
    {
        return $this->generatedFile;
    }

    /**
     * @param mixed $zipCodeMds
     */
    public function setZipCodeMds($zipCodeMds)
    {
        $this->zipCodeMds = $zipCodeMds;
    }

    /**
     * @return mixed
     */
    public function getZipCodeMds()
    {
        return $this->zipCodeMds;
    }



}
