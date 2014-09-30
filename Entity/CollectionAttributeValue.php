<?php

namespace Novactive\EzPublishFormGeneratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CollectionAttributeValue
 *
 * @ORM\Table(name="TBL_collection_attribute_value", indexes={@ORM\Index(name="formCollectionID_idx", columns={"FORM_COLLECTION_id"}), @ORM\Index(name="formAttributeID_idx", columns={"FORM_ATTRIBUTE_id"})})
 * @ORM\Entity
 */
class CollectionAttributeValue
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
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="key_select", type="string", length=255, nullable=true)
     */
    private $key;

    /**
     * @var \Collection
     *
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="collectionAttributeValue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FORM_COLLECTION_id", referencedColumnName="id")
     * })
     */
    private $formCollection;

    /**
     * @var \Attribute
     *
     * @ORM\ManyToOne(targetEntity="Attribute")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FORM_ATTRIBUTE_id", referencedColumnName="id", onDelete="cascade")
     * })
     */
    private $formAttribute;

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
     * Set value
     *
     * @param string $value
     * @return CollectionAttributeValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return CollectionAttributeValue
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set formCollection
     *
     * @param Collection $formCollection
     * @return $this
     */
    public function setFormCollection(Collection $formCollection = null)
    {
        $this->formCollection = $formCollection;

        return $this;
    }

    /**
     * Get formCollection
     *
     * @return Collection
     */
    public function getFormCollection()
    {
        return $this->formCollection;
    }

    /**
     * Set formAttribute
     *
     * @param Attribute $formAttribute
     * @return $this
     */
    public function setFormAttribute(Attribute $formAttribute = null)
    {
        $this->formAttribute = $formAttribute;

        return $this;
    }

    /**
     * Get formAttribute
     *
     * @return Attribute
     */
    public function getFormAttribute()
    {
        return $this->formAttribute;
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
