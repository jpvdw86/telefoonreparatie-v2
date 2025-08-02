<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="Brands", indexes={
 *     @ORM\Index(name="brand_idx", columns={"slug"} )
 * })
 */
class Brand
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Model", mappedBy="brand")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $models;

    /**
     * @ORM\OneToMany(targetEntity="ModelGroup", mappedBy="brand")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $groups;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $background;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $mainContent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $firstContent;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $status = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $inFooter = true;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $sort = 0;

    public function __construct()
    {
        $this->models = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    /**
     * @return Model[] | ArrayCollection
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @param mixed $models
     */
    public function setModels($models)
    {
        $this->models = $models;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getMainContent()
    {
        return $this->mainContent;
    }

    /**
     * @param mixed $mainContent
     */
    public function setMainContent($mainContent)
    {
        $this->mainContent = $mainContent;
    }

    /**
     * @return mixed
     */
    public function getFirstContent()
    {
        return $this->firstContent;
    }

    /**
     * @param mixed $firstContent
     */
    public function setFirstContent($firstContent)
    {
        $this->firstContent = $firstContent;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param mixed $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param mixed $metaTitle
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return mixed
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * @param mixed $background
     */
    public function setBackground($background): void
    {
        $this->background = $background;
    }

    /**
     * @return mixed
     */
    public function getInFooter()
    {
        return $this->inFooter;
    }

    /**
     * @param mixed $inFooter
     */
    public function setInFooter($inFooter): void
    {
        $this->inFooter = $inFooter;
    }

    /**
     * @depracated use getGroups
     * @return mixed
     */
    public function getModelGroups()
    {
        return $this->groups;
    }

    /**
     * @depracated use setGroup
     * @param mixed $modelGroups
     */
    public function setModelGroups($groups): void
    {
        $this->groups = $groups;
    }


}