<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FacebookreviewsRepository")
 * @ORM\Table(name="Facebook_reviews", indexes={
 *     @ORM\Index(name="rating_idx", columns={"rating"})
 * })
 */
class Facebookreviews
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $facebookId;

    /**
     * @ORM\Column(type="string")
     */
    protected $facebookName;

    /**
     * @ORM\Column(type="string")
     */
    protected $facebookUserImage;

    /**
     * @ORM\Column(type="integer")
     */
    protected $rating;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="text")
     */
    protected $reviewtext;

    /**
     * @ORM\Column(type="text")
     */
    protected $domain;

    /**
     * @return mixed
     */
    public function getReviewtext()
    {
        return $this->reviewtext;
    }

    /**
     * @param mixed $reviewtext
     */
    public function setReviewtext($reviewtext)
    {
        $this->reviewtext = $reviewtext;
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
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param mixed $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return mixed
     */
    public function getFacebookName()
    {
        return $this->facebookName;
    }

    /**
     * @param mixed $facebookName
     */
    public function setFacebookName($facebookName)
    {
        $this->facebookName = $facebookName;
    }

    /**
     * @return mixed
     */
    public function getFacebookUserImage()
    {
        return $this->facebookUserImage;
    }

    /**
     * @param mixed $facebookUserImage
     */
    public function setFacebookUserImage($facebookUserImage)
    {
        $this->facebookUserImage = $facebookUserImage;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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

}