<?php

namespace App\Repository;

use App\Entity\User; 
use App\Entity\Person;
use Doctrine\ORM\Query ; 
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
        
        public function searchTeamMembers(array $filters,User $user): Query 
        {
            if (!$user) {
                throw new \Exception('Aucun utilisateur connecté.');
            }

            $qb = $this->createQueryBuilder('person')
                ->where('person.manager = :manager')
                ->setParameter('manager', $user);

            //FILTRE PAR LASTNAME
            if (!empty($filters['last_name'])) {
                $qb->andWhere('person.lastName LIKE :last_name')
                ->setParameter('last_name', '%' . $filters['last_name'] . '%');
            }

            //FILTRE PAR FIRSTNAME
            if (!empty($filters['first_name'])) {
                $qb->andWhere('person.firstName LIKE :first_name')
                ->setParameter('first_name', '%' . $filters['first_name'] . '%');
            }

            //FILTRE PAR EMAIL
            if (!empty($filters['email'])) {
                $qb->andWhere('person.email LIKE :email')
                ->setParameter('email', '%' . $filters['email'] . '%');
            }

            //FILTRE PAR LE POSTE
            if (!empty($filters['position_name'])) {
                $qb->andWhere('pos.name = :position_name')
                ->setParameter('position_name', $filters['position_name']);
            }

            //FILTRE PAR NOMBRE DE CONGE ATTENTION, CALCUL A FAIRE EN AMONT
            if (!empty($filters['conges'])) {
                $qb->andWhere('person.conges = :conges')
                ->setParameter('conges', $filters['conges']);
            }

            //ATTENTION, MANQUE LE FILTRE PAR LE NOMBRE DE CONGE

            return $qb->getQuery();
        }



        

    
}
