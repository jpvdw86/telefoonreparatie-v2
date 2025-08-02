<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="email_messages" )
 */
class Message {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="domain", type="string", nullable=false)
     */
    protected $domain;

    /**
     * @ORM\Column(name="type_template", type="string", nullable=false)
     */
    protected $typeTemplate;

    /**
     * @ORM\Column(name="message_id", type="string", nullable=false)
     */
    protected $messageId;

    /**
     * @ORM\Column(type="string", length=16777216, nullable=false)
     */
    protected $message;

    /**
     * @ORM\Column(name="email_body", type="string", length=16777216, nullable=true)
     */
    protected $emailBody;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $incomming;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $sendDate;

    /**
     * @deprecated use relation messagedevices
     * @ORM\Column(type="string", nullable=true)
     */
    protected $color;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $appointmentType;

    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="messages")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="MessageDevice", mappedBy="message")
     */
    private $devices;

    /**
     * @deprecated use relation messagedevices
     * @ORM\ManyToOne(targetEntity="Model", inversedBy="messages")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     */
    private $model;

    /**
     * @deprecated use relation messagedevices
     * @ORM\ManyToMany(targetEntity="Repair", mappedBy="messages")
     */
    private $repairs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageTracking", mappedBy="message")
     */
    protected $tracking;

    public function __construct()
    {
        $this->sendDate = new \DateTime();
        $this->repairs = new ArrayCollection();
        $this->devices = new ArrayCollection();
        $this->tracking = new ArrayCollection();
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
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param mixed $messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
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
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * @param mixed $sendDate
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getIncomming()
    {
        return $this->incomming;
    }

    /**
     * @param mixed $incomming
     */
    public function setIncomming($incomming)
    {
        $this->incomming = $incomming;
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @deprecated
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getRepairs()
    {
        return $this->repairs;
    }

    /**
     * @deprecated
     * @param mixed $repair
     */
    public function addRepair(Repair $repair)
    {
        if(!$this->repairs->contains($repair)){
            $this->repairs->add($repair);
            $repair->addMessage($this);

        }
    }

    /**
     * @deprecated
     * @param mixed $repair
     */
    public function removeRepair(Repair $repair)
    {
        if($this->repairs->contains($repair)){
            $this->repairs->removeElement($repair);
        }
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @deprecated
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getEmailBody()
    {
        return $this->emailBody;
    }

    /**
     * @param mixed $emailBody
     */
    public function setEmailBody($emailBody)
    {
        $this->emailBody = $emailBody;
    }

    /**
     * @return mixed
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * @param mixed $tracking
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return mixed
     */
    public function getTypeTemplate()
    {
        return $this->typeTemplate;
    }

    /**
     * @param mixed $typeTemplate
     */
    public function setTypeTemplate($typeTemplate)
    {
        $this->typeTemplate = $typeTemplate;
    }

    /**
     * @return mixed
     */
    public function getAppointmentType()
    {
        return $this->appointmentType;
    }

    /**
     * @param mixed $appointmentType
     */
    public function setAppointmentType($appointmentType): void
    {
        $this->appointmentType = $appointmentType;
    }

    /**
     * @return ArrayCollection
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * @param MessageDevice $messageDevice
     */
    public function addDevice(MessageDevice $messageDevice)
    {
        if(!$this->devices->contains($messageDevice)){
            $this->devices->add($messageDevice);
        }
    }

    /**
     * @param ArrayCollection $devices
     */
    public function setDevices(ArrayCollection $devices): void
    {
        $this->devices = $devices;
    }
}