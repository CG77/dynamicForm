<?php

namespace Novactive\EzPublishFormGeneratorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="TBL_page", indexes={@ORM\Index(name="ENTITYID_idx", columns={"ENTITY_id"})})
 * @ORM\Entity(repositoryClass="Novactive\EzPublishFormGeneratorBundle\Repository\PageRepository")
 */
class Page
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
     * @var string
     *
     * @ORM\Column(name="data_text", type="text", nullable=true)
     */
    private $dataText;

    /**
     * @var \Entity
     *
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="pages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ENTITY_id", referencedColumnName="id")
     * })
     */
    private $entity;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="page",cascade={"persist"})
     * @ORM\OrderBy({"placement" = "ASC"})
     */
    private $attributes;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
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
     * Set dataText
     *
     * @param string $dataText
     * @return Page
     */
    public function setDataText($dataText)
    {
        $this->dataText = $dataText;

        return $this;
    }

    /**
     * Get dataText
     *
     * @return string
     */
    public function getDataText()
    {
        return $this->dataText;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return Page
     */
    public function setEntity(Entity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        /** @var Page $page */
        $tab = array('label' => $this->getDataText());
        $attributes = array();
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->toArray();
        }
        $tab['attributes'] = $attributes;
        return $tab;
    }
}
