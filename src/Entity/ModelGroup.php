<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="ModelGroup")
 * @ORM\Entity(repositoryClass="App\Repository\ModelGroupRepository")
 */
class ModelGroup
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand", inversedBy="groups")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    private $brand;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Model", mappedBy="group")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $models;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;


    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @ORM\Column(type="boolean")
     */
    protected $status = true;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $sort = 0;


    public function __construct()
    {
        $this->models = new ArrayCollection();
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


    public function getActiveModels()
    {
        $models = $this->models;
        $m = [];

        /** @var Model $model */
        foreach ($models as $model){
            if($model->getStatus()){
                array_push($m, $model);
            }
        }

        return $m;
    }

}