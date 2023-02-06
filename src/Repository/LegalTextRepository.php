<?php

namespace App\Repository;

use App\Entity\LegalText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LegalText>
 *
 * @method LegalText|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalText|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalText[]    findAll()
 * @method LegalText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalTextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalText::class);
    }

    public function add(LegalText $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LegalText $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
