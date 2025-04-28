<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //POUR CHERCHER LES MANAGERS AVEC LES FILTRES
    public function findManagersWithFilters(array $filters = []): \Doctrine\ORM\Query
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.person', 'p')
            ->join('p.department', 'd')
            ->addSelect('p')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_MANAGER%');

        if (!empty($filters['last_name'])) {
            $qb->andWhere('p.last_name LIKE :last_name')
                ->setParameter('last_name', '%' . $filters['last_name'] . '%');
        }

        if (!empty($filters['first_name'])) {
            $qb->andWhere('p.first_name LIKE :first_name')
                ->setParameter('first_name', '%' . $filters['first_name'] . '%');
        }

        if (!empty($filters['department'])) {
            $qb->andWhere('d.name = :department')
                ->setParameter('department', $filters['department']);
        }

        $qb->orderBy('p.first_name', 'ASC');

        return $qb->getQuery();
    }

    public function findCollaboratorsByManager(int $managerId): array
    {
        return $this->createQueryBuilder('u')
            ->select('p.id AS person_id', 'u.id AS user_id')
            ->join('u.person', 'p')
            ->where('p.manager = :managerId')
            ->setParameter('managerId', $managerId)
            ->getQuery()
            ->getScalarResult();
    }
}
