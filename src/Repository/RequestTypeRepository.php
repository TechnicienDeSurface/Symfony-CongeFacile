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

    public function searchTypeOfRequest(array $filters,  string $order = ''): Query
    {
        $qb = $this->createQueryBuilder('request_type');
    
        // Correction : jointure correcte avec l'entité Request
        $qb->leftJoin('request_type.requests', 'requests');
    
        if (!empty($filters['name'])) {
            $qb->andWhere('request_type.name LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if ($order) {
            $qb->orderBy('request_type.name', $order);
        }
    
        return $qb->getQuery();
    }
}
