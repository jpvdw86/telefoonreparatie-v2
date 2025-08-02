<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class OpeningHoursRepository extends EntityRepository
{

    /**
     * @param $template
     * @param $domain
     * @return array
     */
    function getAllOpeningsHoursByTypeAndDomain($template, $domain){
        return $this->createQueryBuilder('openingHours')
            ->where('openingHours.domain = :domain')
            ->andWhere('openingHours.typeTemplate = :template')
            ->setParameter('template', $template)
            ->setParameter('domain', $domain)
            ->orderBy('openingHours.day', 'ASC')
            ->getQuery()
            ->useResultCache(true, 3600)
            ->getResult();
    }
}
