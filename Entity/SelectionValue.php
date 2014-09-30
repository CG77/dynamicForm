<?php

namespace Novactive\EzPublishFormGeneratorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SelectionValue
 *
 * @ORM\Table(name="TBL_selection_value", indexes={@ORM\Index(name="FormAttributeID_idx", columns={"FORM_ATTRIBUTE_ID"})})
 * @ORM\Entity
 */
class SelectionValue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="text", nullable=true)
     */
    protected $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="placement", type="integer", nullable=true)
     */
    protected $placement;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=45, nullable=true)
     */
    protected $value;

    /**
     * @var integer
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="integer", nullable=true)
     */
    protected $created;

    /**
     * @var integer
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified", type="integer", nullable=true)
     */
    protected $modified;

    /**
     * @var Attribute
     *
     * @ORM\ManyToOne(targetEntity="Attribute", inversedBy="selectionValues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FORM_ATTRIBUTE_ID", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $formAttribute;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Attribute", cascade={"remove", "merge", "persist"}, inversedBy="selectionAttributes")
     * @ORM\JoinTable(name="JNT_selection_attribute",
     *      joinColumns={@ORM\JoinColumn(name="selection_value_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sub_attribute_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $subAttributes;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $subAttributesText;

    public function __construct()
    {
        $this->subAttributes = new ArrayCollection();
        $this->subAttributesText = new ArrayCollection();
    }

    /**
     * @param Collection $subAttributes
     */
    public function setSubAttributes($subAttributes)
    {
        if (is_array($subAttributes)) {
            $subAttributes = new ArrayCollection($subAttributes);
        }
        $this->subAttributes = $subAttributes;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubAttributes()
    {
        return $this->subAttributes;
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
     * Set value
     *
     * @param string $value
     * @return SelectionValue
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
     * Set placement
     *
     * @param integer $placement
     * @return SelectionValue
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
     * Set key
     *
     * @param string $libelle
     * @return SelectionValue
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set created
     *
     * @param integer $created
     * @return SelectionValue
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param integer $modified
     * @return SelectionValue
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return integer
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set formAttribute
     *
     * @param Attribute $formAttribute
     * @return SelectionValue
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

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subAttributes
     */
    public function setSubAttributesText($subAttributes)
    {

        $this->mergeSubAttributes("text", $subAttributes);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubAttributesText()
    {
        return $this->getSubAttributesByType("text");
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subAttributes
     */
    public function setSubAttributesInteger($subAttributes)
    {

        $this->mergeSubAttributes("integer", $subAttributes);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubAttributesInteger()
    {
        return $this->getSubAttributesByType("integer");
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subAttributes
     */
    public function setSubAttributesDate($subAttributes)
    {

        $this->mergeSubAttributes("date", $subAttributes);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubAttributesDate()
    {
        return $this->getSubAttributesByType("date");
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subAttributes
     */
    public function setSubAttributesTextarea($subAttributes)
    {

        $this->mergeSubAttributes("textarea", $subAttributes);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubAttributesTextarea()
    {
        return $this->getSubAttributesByType("textarea");
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subAttributes
     */
    public function setSubAttributesEmail($subAttributes)
    {

        $this->mergeSubAttributes("email", $subAttributes);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubAttributesEmail()
    {
        return $this->getSubAttributesByType("email");
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subAttributes
     */
    public function setSubAttributesSelection($subAttributes)
    {
        $this->mergeSubAttributes("selection", $subAttributes);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubAttributesSelection()
    {
        return $this->getSubAttributesByType("selection");
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubAttributesGrid()
    {
        return $this->getSubAttributesByType("grid");
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subAttributes
     */
    public function setSubAttributesGrid($subAttributes)
    {
        $this->mergeSubAttributes("grid", $subAttributes);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubAttributesScale()
    {
        return $this->getSubAttributesByType("scale");
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subAttributes
     */
    public function setSubAttributesScale($subAttributes)
    {
        $this->mergeSubAttributes("scale", $subAttributes);
    }

    /**
     * @param $type
     * @param $newCollection
     */
    public function mergeSubAttributes($type, $newCollection)
    {
        $old = array();
        $new = array();

        // get all old attributes
        /** @var Attribute $attribute */
        foreach ($this->subAttributes as $attribute) {
            if ($attribute->getDataTypeString() == $type) {
                $old[$attribute->getId()] = $attribute;
            }
        }

        foreach ($newCollection as $attribute) {
            if ($attribute->getId() && array_key_exists($attribute->getId(), $old)) {
                unset($old[$attribute->getId()]);
                continue;
            }
            $attribute->setDataTypeString($type);
            if ($this->getFormAttribute()) {
                $attribute->setEzcontentLanguageId($this->getFormAttribute()->getEzcontentLanguageId());
                $attribute->setStatus($this->getFormAttribute()->getStatus());
            }

            $new[] = $attribute;
        }

        foreach ($old as $attribute) {
            $this->subAttributes->removeElement($attribute);
        }
        foreach ($new as $attribute) {
            $this->subAttributes->add($attribute);
        }
    }

    public function getSubAttributesByType($type)
    {
        $collection = new ArrayCollection();

        /** @var Attribute $attribute */
        foreach ($this->subAttributes as $attribute) {
            if ($attribute->getDataTypeString() == $type) {
                $collection[] = $attribute;
            }
        }
        return $collection;
    }

    public function unsetFormAttribute()
    {
        $this->formAttribute = null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $tab = array(
            'label' => $this->getLibelle(),
            'value' => $this->getValue(),
            'sub_attributes' => array()
        );

        foreach ($this->getSubAttributes() as $subAttribute) {
            $tab['sub_attributes'][] = $subAttribute->toArray();
        }
        return $tab;
    }
}
