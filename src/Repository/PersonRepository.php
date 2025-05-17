<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Person;
use App\Entity\Department;
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

    public function searchTeamMembers(array $filters,Department $department, User $manager, $order): Query
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

        // RESTRICTIONS SUR LE DEPARTEMENT DES COLLABORATEURS
        if (!empty($department)) {
            $qb->andWhere('person.department = :department_id')
                ->setParameter('department_id',$department->getId());
        }

        // RESTRICTIONS SUR LE MANAGER DES COLLABORATEURS
        if (!empty($manager)) {
            $qb->andWhere('person.manager = :id')
                ->setParameter('id',$manager->getId());
        }

        if ($order) {
            $qb->orderBy('person.id', $order);
        }
        return $qb->getQuery();
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('person');

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

    public function getPersonById(int $id): ?Person
    {
        return $this->createQueryBuilder('person')
            ->andWhere('person.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getPersonByIdDepartment(int $id): ?array
    {
        return $this->createQueryBuilder('person')
            ->andWhere('person.department = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    //Trouvé des personnes par une position pour la vérification de suppression de postes
    public function findPersonByPosition(int $id): ?int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->join('p.position', 'position') 
            ->where('position.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult(); 
    }

    //Trouvé des personnes par un département pour la vérification de suppression de postes
    public function findPersonByDepartment(int $id): ?int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->join('p.department', 'department') 
            ->where('department.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult(); 
    }

}
