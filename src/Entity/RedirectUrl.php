<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="Redirect_url", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="slug_idx", columns={"from_slug", "domain", "type_template"})
 * },
 * indexes={
 *     @ORM\Index(name="domain_idx", columns={"domain"}),
 *     @ORM\Index(name="type_template_idx", columns={"type_template"}),
 * })
 */
class RedirectUrl
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="from_slug", type="string", nullable=false)
     */
    protected $fromSlug;

    /**
     * @ORM\Column(name="to_slug", type="string", nullable=false)
     */
    protected $toSLug;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $status = true;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $domain;

    /**
     * @ORM\Column(name="type_template", type="string", nullable=false)
     */
    protected $typeTemplate = 'bus';

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
    public function getFromSlug()
    {
        return $this->fromSlug;
    }

    /**
     * @param mixed $fromSlug
     */
    public function setFromSlug($fromSlug)
    {
        $this->fromSlug = $fromSlug;
    }

    /**
     * @return mixed
     */
    public function getToSLug()
    {
        return $this->toSLug;
    }

    /**
     * @param mixed $toSLug
     */
    public function setToSLug($toSLug)
    {
        $this->toSLug = $toSLug;
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
}
