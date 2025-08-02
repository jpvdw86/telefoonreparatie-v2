<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="Models", indexes={
 *     @ORM\Index(name="model_idx", columns={"slug"})
 * })
 */
class Model
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="models")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    private $brand;

    /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="App\Entity\ModelGroup", inversedBy="models")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=true )
     */
    private $group;

    /**
     * @ORM\OneToMany(targetEntity="Repair", mappedBy="model")
     */
    private $repair;

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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $mainContent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $firstContent;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="model")
     */
    protected $messages;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $status = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $inFooter = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isPopular = true;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $sort = 0;

    public function __construct() {
        $this->repair = new ArrayCollection();
        $this->messages = new ArrayCollection();
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
     * @return Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
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
     * @return Repair[] | ArrayCollection
     */
    public function getRepair()
    {
        return $this->repair;
    }

    /**
     * @return array
     */
    public function getRepairOrderdBy($type = 'ASC')
    {
        $output = [];
        if($this->getRepair()->first()){
            foreach($this->getRepair() as $repair){
                if($repair->getRepairOption()) {
                    $output[$repair->getRepairOption()->getSort()] = $repair;
                }
            }
            ksort($output);
            if($type == 'DESC'){
                krsort($output);
            }
        }
        return $output;
    }

    /**
     * @param mixed $repair
     */
    public function setRepair($repair)
    {
        $this->repair = $repair;
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
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param mixed $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
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
     * @return mixed
     */
    public function getIsPopular()
    {
        return $this->isPopular;
    }

    /**
     * @param mixed $isPopular
     */
    public function setIsPopular($isPopular): void
    {
        $this->isPopular = $isPopular;
    }

}