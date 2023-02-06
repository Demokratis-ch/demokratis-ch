<?php

namespace App\Repository;

use App\Entity\ChosenModification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChosenModification>
 *
 * @method ChosenModification|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChosenModification|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChosenModification[]    findAll()
 * @method ChosenModification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChosenModificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChosenModification::class);
    }

    public function save(ChosenModification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChosenModification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
