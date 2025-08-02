<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Repair")
 */
class Repair
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Model", inversedBy="repair")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     */
    private $model;

    /**
     * @ORM\ManyToOne(targetEntity="RepairOptions", inversedBy="repairOptions")
     * @ORM\JoinColumn(name="repair_option_id", referencedColumnName="id")
     */
    private $repairOption;

    /**
     * @ORM\Column(name="price_nl", type="decimal", scale=2)
     */
    protected $priceNl;

    /**
     * @ORM\Column(name="price_be", type="decimal", scale=2)
     */
    protected $priceBe;

    /**
     * @ORM\Column(name="price_from_nl", type="decimal", scale=2)
     */
    protected $priceFromNl;

    /**
     * @ORM\Column(name="price_from_be", type="decimal", scale=2)
     */
    protected $priceFromBe;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $repairTimeFrom;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $repairTimeUntil;


    /**
     * @ORM\ManyToMany(targetEntity="Message", inversedBy="repairs")
     * @ORM\JoinTable(name="messages_repairs")
     */
    private $messages;

    /**
     * @ORM\ManyToMany(targetEntity="MessageDevice", inversedBy="repairs")
     * @ORM\JoinTable(name="email_messages_device_repairs")
     */
    private $messageDevice;

    public function __construct()
    {
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
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return RepairOptions
     */
    public function getRepairOption()
    {
        return $this->repairOption;
    }

    /**
     * @param mixed $repairOption
     */
    public function setRepairOption($repairOption)
    {
        $this->repairOption = $repairOption;
    }

    /**
     * @return mixed
     */
    public function getPriceNl()
    {
        return $this->priceNl;
    }

    /**
     * @param mixed $priceNl
     */
    public function setPriceNl($priceNl)
    {
        $this->priceNl = $priceNl;
    }

    /**
     * @return mixed
     */
    public function getPriceBe()
    {
        return $this->priceBe;
    }

    /**
     * @param mixed $priceBe
     */
    public function setPriceBe($priceBe)
    {
        $this->priceBe = $priceBe;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param mixed $message
     */
    public function addMessage(Message $message)
    {
        if(!$this->messages->contains($message)){
            $this->messages->add($message);
            $message->addRepair($this);
        }
    }

    /**
     * @param mixed $message
     */
    public function removeMessage(Message $message)
    {
        if($this->messages->contains($message)){
            $this->messages->removeElement($message);
        }
    }

    /**
     * @return mixed
     */
    public function getPriceFromNl()
    {
        return $this->priceFromNl;
    }

    /**
     * @param mixed $priceFromNl
     */
    public function setPriceFromNl($priceFromNl): void
    {
        $this->priceFromNl = $priceFromNl;
    }

    /**
     * @return mixed
     */
    public function getPriceFromBe()
    {
        return $this->priceFromBe;
    }

    /**
     * @param mixed $priceFromBe
     */
    public function setPriceFromBe($priceFromBe): void
    {
        $this->priceFromBe = $priceFromBe;
    }

    /**
     * @return mixed
     */
    public function getRepairTimeFrom()
    {
        return $this->repairTimeFrom;
    }

    /**
     * @param mixed $repairTimeFrom
     */
    public function setRepairTimeFrom($repairTimeFrom): void
    {
        $this->repairTimeFrom = $repairTimeFrom;
    }

    /**
     * @return mixed
     */
    public function getRepairTimeUntil()
    {
        return $this->repairTimeUntil;
    }

    /**
     * @param mixed $repairTimeUntil
     */
    public function setRepairTimeUntil($repairTimeUntil): void
    {
        $this->repairTimeUntil = $repairTimeUntil;
    }


    /**
     * @return mixed
     */
    public function getMessageDevice()
    {
        return $this->messageDevice;
    }

    /**
     * @param mixed $messageDevice
     */
    public function setMessageDevice($messageDevice): void
    {
        $this->messageDevice = $messageDevice;
    }

    /**
     * @param MessageDevice $messageDevice
     */
    public function addMessageDevice(MessageDevice $messageDevice)
    {
        if(!$this->messageDevice->contains($messageDevice)){
            $this->messageDevice->add($messageDevice);
            $messageDevice->addRepair($this);
        }
    }

    /**
     * @param MessageDevice $messageDevice
     */
    public function removeMessageDevice(MessageDevice $messageDevice)
    {
        if($this->messageDevice->contains($messageDevice)){
            $this->messageDevice->removeElement($messageDevice);
        }
    }

}