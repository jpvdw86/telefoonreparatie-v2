<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="teambase_invoice", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="teambase_invoice_id_idx", columns={"teambase_invoice_id"})
 * })
 */
class TeambaseInvoice
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="teambase_invoice_id", type="string", nullable=false)
     */
    protected $teambaseInvoiceId;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $sentDate;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $data;

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
    public function getTeambaseInvoiceId()
    {
        return $this->teambaseInvoiceId;
    }

    /**
     * @param mixed $teambaseInvoiceId
     */
    public function setTeambaseInvoiceId($teambaseInvoiceId): void
    {
        $this->teambaseInvoiceId = $teambaseInvoiceId;
    }

    /**
     * @return mixed
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * @param mixed $sentDate
     */
    public function setSentDate($sentDate): void
    {
        $this->sentDate = $sentDate;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

}