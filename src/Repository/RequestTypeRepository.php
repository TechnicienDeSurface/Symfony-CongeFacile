<?php

namespace App\Repository;

use App\Entity\RequestType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<RequestType>
 */
class RequestTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestType::class);
    }

    public function searchTypeOfRequest(array $filters): Query
    {
        $qb = $this->createQueryBuilder('requestType');
    
        // Jointure avec l'entité requesttype vers request
        $qb->join('request.requestType', 'request');

        // FILTRE PAR LASTNAME
        if (!empty($filters['name'])) {
            $qb->andWhere('request.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        return $qb->getQuery(); // Retourne une Query au lieu du QueryBuilder
    }
}
