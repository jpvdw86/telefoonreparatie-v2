<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OpeningHoursRepository")
 * @ORM\Table(name="OpeningsHours")
 * @UniqueEntity(
 *     fields={"day", "domain", "typeTemplate"},
 *     errorPath="day",
 *     message="Deze dag komt al voor in combinate van type website and domein."
 * )
 */
class OpeningHours {

    Const MONDAY = 1;
    Const TUESDAY = 2;
    Const WEDNESDAY = 3;
    Const THURSDAY = 4;
    Const FRIDAY = 5;
    Const SATURDAY = 6;
    Const SUNDAY = 7;

    Const DAYNAMES = [
        self::MONDAY => 'Maandag',
        self::TUESDAY => 'Dinsdag',
        self::WEDNESDAY => 'Woensdag',
        self::THURSDAY => 'Donderdag',
        self::FRIDAY => 'Vrijdag',
        self::SATURDAY => 'Zaterdag',
        self::SUNDAY => 'Zondag'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $openingHour;

    /**
     * @ORM\Column(type="time")
     */
    private $closingHour;

    /**
     * @ORM\Column(type="integer")
     */
    private $day;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $domain;

    /**
     * @ORM\Column(name="type_template", type="string", nullable=false)
     */
    protected $typeTemplate = 'bus';

    /**
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    protected $comment;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOpeningHour()
    {
        return $this->openingHour;
    }

    /**
     * @param $openingHour
     */
    public function setOpeningHour($openingHour): void
    {
        $this->openingHour = $openingHour;
    }

    /**
     * @return mixed
     */
    public function getClosingHour()
    {
        return $this->closingHour;
    }

    /**
     * @param mixed $closingHour
     */
    public function setClosingHour($closingHour): void
    {
        $this->closingHour = $closingHour;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @return array
     */
    public function getDayNames(){
        return self::DAYNAMES;
    }

    /**
     * @return mixed
     */
    public function getDayName()
    {
        if($this->day) {
            return self::DAYNAMES[$this->day];
        }
    }

    /**
     * @param mixed $day
     */
    public function setDay($day): void
    {
        $this->day = $day;
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
    public function setDomain($domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getTypeTemplate(): string
    {
        return $this->typeTemplate;
    }

    /**
     * @param string $typeTemplate
     */
    public function setTypeTemplate(string $typeTemplate): void
    {
        $this->typeTemplate = $typeTemplate;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
