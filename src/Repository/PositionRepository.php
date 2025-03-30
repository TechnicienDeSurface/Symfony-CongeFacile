<?php

namespace App\Repository;

use App\Entity\Position;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Position>
 */
class PositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    public function findPagination(int $page, int $limit)
    {
        $offset = ($page - 1) * $limit;
    
        $query = $this->createQueryBuilder('p')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();
    
        return ($query);
    }

    public function searchTypeOfRequest(array $filters,  string $order = ''): Query
    {
        $qb = $this->createQueryBuilder('position');
    
        // Correction : jointure correcte avec l'entité person
        $qb->leftJoin('position.persons', 'person');
    
        if (!empty($filters['name'])) {
            $qb->andWhere('position.name LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if ($order) {
            $qb->orderBy('position.name', $order);
        }
    
        return $qb->getQuery();
    }

}
