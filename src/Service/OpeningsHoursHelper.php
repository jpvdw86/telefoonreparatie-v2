<?php

namespace App\Service;

use App\Entity\OpeningHours;
use Doctrine\ORM\EntityManagerInterface;

class OpeningsHoursHelper {

    protected $entityManager;

    /**
     * OpeningsHoursHelper constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getHours(){
        $hours = $this->entityManager->getRepository(OpeningHours::class)->getAllOpeningsHoursByTypeAndDomain(
            getenv('TEMPLATE'),
            getenv('DOMAIN')
        );
        return $hours;
    }

}