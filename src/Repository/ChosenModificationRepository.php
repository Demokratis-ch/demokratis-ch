<?php

namespace App\Repository;

use App\Entity\ChosenModification;
use App\Entity\Statement;
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

    /**
     * @return array<int, ChosenModification>
     */
    public function findByStatementIndexed(Statement $statement): array
    {
        /** @var ChosenModification[] $rows */
        $rows = $this->createQueryBuilder('c')
            ->addSelect('ms')
            ->addSelect('m')
            ->leftJoin('c.modificationStatement', 'ms')
            ->leftJoin('ms.modification', 'm')
            ->where('c.statement = :statement')
            ->setParameter('statement', $statement)
            ->getQuery()
            ->getResult()
        ;

        $results = [];
        foreach ($rows as $chosenModification) {
            $results[$chosenModification->getParagraph()->getId()] = $chosenModification;
        }

        return $results;
    }
}
