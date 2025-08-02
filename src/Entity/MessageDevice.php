<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="email_message_devices" )
 */
class MessageDevice {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $color;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="devices")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="Model")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     */
    private $model;

    /**
     * @ORM\ManyToMany(targetEntity="Repair", mappedBy="messageDevice")
     */
    private $repairs;

    public function __construct()
    {
        $this->repairs = new ArrayCollection();
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color): void
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model): void
    {
        $this->model = $model;
    }

    /**
     * @return ArrayCollection
     */
    public function getRepairs()
    {
        return $this->repairs;
    }

    /**
     * @param ArrayCollection $repairs
     */
    public function setRepairs(ArrayCollection $repairs): void
    {
        $this->repairs = $repairs;
    }

    /**
     * @param mixed $repair
     */
    public function addRepair(Repair $repair)
    {
        if(!$this->repairs->contains($repair)){
            $this->repairs->add($repair);
            $repair->addMessageDevice($this);
        }
    }

    /**
     * @param mixed $repair
     */
    public function removeRepair(Repair $repair)
    {
        if($this->repairs->contains($repair)){
            $this->repairs->removeElement($repair);
        }
    }
}