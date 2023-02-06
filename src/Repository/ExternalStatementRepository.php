<?php

namespace App\Repository;

use App\Entity\ExternalStatement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExternalStatement>
 *
 * @method ExternalStatement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalStatement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalStatement[]    findAll()
 * @method ExternalStatement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalStatementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalStatement::class);
    }

    public function save(ExternalStatement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExternalStatement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
