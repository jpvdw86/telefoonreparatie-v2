<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Facebookreviews;


class ModelGroupRepository extends EntityRepository
{
    /**
     * @param $type
     * @return mixed
     */
    public function getAllMakesWithType($type){
        $query = $this->createQueryBuilder('q')
            ->where('q.type = :type')
            ->groupby('q.brand')
            ->setParameter('type', $type)
            ->getQuery();

        return $query->getResult();

    }
}