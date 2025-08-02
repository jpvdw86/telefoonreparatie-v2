<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="Page", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="slug_idx", columns={"slug", "domain", "type_template"})
 * },
 * indexes={
 *     @ORM\Index(name="domain_idx", columns={"domain"}),
 *     @ORM\Index(name="type_template_idx", columns={"type_template"}),
 * })
 * @UniqueEntity(
 *      fields={"slug","domain", "type_template"},
 *      message="Url in combinatie slug, domeinnaam bestaat al."
 * )
 */
class Page
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="slug", type="string", nullable=false)
     */
    protected $slug;

    /**
     * @ORM\Column(name="menu_link_name", type="string", nullable=true)
     */
    protected $menuLinkName;

    /**
     * @ORM\Column(name="menu_category", type="string", nullable=false)
     */
    protected $menuCategory = '';

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $status = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $inFooter = true;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $template;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaKeywords;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $firstContent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $mainContent;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $domain;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $backgroundImage;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $subTitle;


    /**
     * @ORM\Column(name="type_template", type="string", nullable=false)
     */
    protected $typeTemplate = 'bus';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Model")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     */
    protected $model;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    protected $brand;


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
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
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
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param mixed $metaKeywords
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
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
    public function getMenuLinkName()
    {
        return $this->menuLinkName;
    }

    /**
     * @param mixed $menuLinkName
     */
    public function setMenuLinkName($menuLinkName): void
    {
        $this->menuLinkName = $menuLinkName;
    }

    /**
     * @return mixed
     */
    public function getMenuCategory()
    {
        return $this->menuCategory;
    }

    /**
     * @param mixed $menuCategory
     */
    public function setMenuCategory($menuCategory): void
    {
        $this->menuCategory = $menuCategory;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImage()
    {
        return $this->backgroundImage;
    }

    /**
     * @param mixed $backgroundImage
     */
    public function setBackgroundImage($backgroundImage): void
    {
        $this->backgroundImage = $backgroundImage;
    }

    /**
     * @return mixed
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @param mixed $subTitle
     */
    public function setSubTitle($subTitle): void
    {
        $this->subTitle = $subTitle;
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


}
