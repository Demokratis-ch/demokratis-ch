<?php

namespace App\Repository;

use App\Entity\UnknownInstitution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UnknownInstitution>
 *
 * @method UnknownInstitution|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnknownInstitution|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnknownInstitution[]    findAll()
 * @method UnknownInstitution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnknownInstitutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnknownInstitution::class);
    }

    public function save(UnknownInstitution $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UnknownInstitution $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
