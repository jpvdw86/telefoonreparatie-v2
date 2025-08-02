<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Facebookreviews;


class FacebookreviewsRepository extends EntityRepository
{

    /**
     * Returns value by key and cached for 6 hours
     * @param string $domain
     * @return mixed|null
     */
    public function getAverage($domain = 'nl'){
        if($key = $this->createQueryBuilder('fb')
            ->select('AVG(fb.rating) AS avg_rating, COUNT(fb.id) as total_amount')
            ->where('fb.domain = :domein')
            ->setParameter('domein', $domain)
            ->getQuery()
            ->useResultCache(true, 86400)
            ->getArrayResult()
        ){
            return $key[0];
        }
        return null;
    }

    /**
     * @param string $domain
     * @param int $amount
     * @param int $minRating
     * @return array
     * Returns output cached for 12 hours
     */
    public function getReviews($domain = 'nl', $amount = 12, $minRating = 60){
        return $this->createQueryBuilder('fb')
            ->select('fb')
            ->where('fb.domain = :domain')
            ->andWhere('fb.rating >= :minrating')
            ->setParameter('domain', $domain)
            ->setParameter('minrating', $minRating)
            ->setMaxResults($amount)
            ->orderBy('fb.date', 'DESC')
            ->getQuery()
            ->useResultCache(true, 43200)
            ->getResult();
    }

}