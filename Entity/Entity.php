<?php

namespace Novactive\EzPublishFormGeneratorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity
 *
 * @ORM\Table(name="TBL_entity")
 * @ORM\Entity(repositoryClass="Novactive\EzPublishFormGeneratorBundle\Repository\FormEntityRepository")
 */
class Entity
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=true)
     */
    private $creatorId;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @var integer
     *
     * @ORM\Column(name="EZCONTENT_LANGUAGE_id", type="integer", nullable=false)
     */
    private $ezcontentLanguageId;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Page", mappedBy="entity",cascade={"persist"})
     */
    private $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Entity
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
     * Set creatorId
     *
     * @param integer $creatorId
     * @return Entity
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
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
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
     * Set ezcontentLanguageId
     *
     * @param integer $ezcontentLanguageId
     * @return Entity
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
     * @param \Doctrine\Common\Collections\ArrayCollection $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $pages = array();
        foreach ($this->getPages() as $page) {

            $pages[] = $page->toArray();
        }
        return $pages;
    }

    public function __clone() {
        $pages = $this->getPages();
        $this->pages = new ArrayCollection();
        if(!empty($pages)){
            foreach ($pages as $page) {
                $newPage = clone $page;
                $this->pages->add($newPage);
                $newPage->setEntity($this);
                $attributes = $newPage->getAttributes();
                $cAttributes = new ArrayCollection();
                foreach ($attributes as $attribute) {
                    $newAttribute = clone $attribute;
                    $newAttribute->setEntity($this);
                    $newAttribute->setPage($newPage);
                    $cAttributes->add($newAttribute);
                }
                $newPage->setAttributes($cAttributes);

            }
        }
    }


}
