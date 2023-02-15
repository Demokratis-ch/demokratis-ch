<?php

namespace App\Repository;

use App\Entity\UserOrganisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserOrganisation>
 *
 * @method UserOrganisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserOrganisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserOrganisation[]    findAll()
 * @method UserOrganisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserOrganisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserOrganisation::class);
    }

    public function save(UserOrganisation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserOrganisation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
