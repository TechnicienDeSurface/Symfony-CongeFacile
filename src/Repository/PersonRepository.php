<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Person;
use Doctrine\ORM\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function searchTeamMembers(array $filters, string $order = ''): Query
    {
        $qb = $this->createQueryBuilder('person');

        // Jointure avec l'entité User pour filtrer par email
        $qb->leftJoin('person.user', 'user');

        $qb->leftJoin('person.position', 'position');

        // FILTRE PAR LASTNAME
        if (!empty($filters['last_name'])) {
            $qb->andWhere('person.last_name LIKE :last_name')
                ->setParameter('last_name', '%' . $filters['last_name'] . '%');
        }

        // FILTRE PAR FIRSTNAME
        if (!empty($filters['first_name'])) {
            $qb->andWhere('person.first_name LIKE :first_name')
                ->setParameter('first_name', '%' . $filters['first_name'] . '%');
        }

        // FILTRE PAR EMAIL
        if (!empty($filters['email'])) {
            $qb->andWhere('user.email LIKE :email')
                ->setParameter('email', '%' . $filters['email'] . '%');
        }

        // FILTRE PAR LE DEPARTEMENT
        if (!empty($filters['name'])) {
            $qb->andWhere('position.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if ($order) {
            $qb->orderBy('person.last_name', $order);
        }


        // // Tri dynamique par total_leave_days
        // $qb->addSelect(
        //     '(
        //         SELECT COUNT(r.id) 
        //         FROM App\Entity\Request r 
        //         WHERE r.collaborator = person
        //     ) AS total_leave_days'
        // );

        // // Tri dynamique par autre attributs comme position ou email peut-être
        // if (!empty($filters['sort_order'])) {
        //     $sortOrder = $filters['sort_order']; // 'ASC' ou 'DESC'
        //     $qb->orderBy('total_leave_days', $sortOrder);
        // } else {
        //     // Par défaut, on trie par autre critère
        //     $qb->orderBy('person.last_name', 'ASC');
        // }

        return $qb->getQuery();
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('person');
        dump($filters);

        // Jointure avec l'entité User pour filtrer par rôle
        $qb->leftJoin('person.user', 'user');

        // FILTRE PAR LASTNAME
        if (!empty($filters['last_name'])) {
            $qb->andWhere('person.last_name LIKE :last_name')
               ->setParameter('last_name', '%' . $filters['last_name'] . '%');
        }

        // FILTRE PAR FIRSTNAME
        if (!empty($filters['first_name'])) {
            $qb->andWhere('person.first_name LIKE :first_name')
               ->setParameter('first_name', '%' . $filters['first_name'] . '%');
        }

        // FILTRE PAR LE DEPARTEMENT
        if (!empty($filters['department'])) {
            $qb->innerJoin('person.department', 'department') // Changer LEFT JOIN en INNER JOIN
               ->andWhere('department.name LIKE :department')
               ->setParameter('department', '%' . $filters['department'] . '%');
        }

        // FILTRE PAR ROLE MANAGER
        $qb->andWhere('user.roles LIKE :role')
           ->setParameter('role', '%ROLE_MANAGER%');

        return $qb->getQuery()->getResult();
    }

}
