<?php

namespace App\Repository;

use App\Entity\Modification;
use App\Entity\Statement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Modification>
 *
 * @method Modification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Modification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Modification[]    findAll()
 * @method Modification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modification::class);
    }

    public function add(Modification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Modification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOpenModificationsIndexed(Statement $statement): array
    {
        $query = $this->createQueryBuilder('m')
            ->leftJoin('m.modificationStatements', 's')
            ->andWhere('s.statement = :statement')
            ->setParameter('statement', $statement)
            ->andWhere('s.refused = :refused')
            ->setParameter('refused', false)
            ->leftJoin('s.chosen', 'x')
            ->andWhere('x.id IS NULL')
            ->orderBy('m.createdAt', 'desc')
            ->addOrderBy('m.id', 'desc')
        ;

        return $this->buildIndexedResult($query->getQuery()->getResult());
    }

    public function findRefusedModificationsIndexed(Statement $statement): array
    {
        $query = $this->createQueryBuilder('m')
            ->leftJoin('m.modificationStatements', 's')
            ->andWhere('s.statement = :statement')
            ->setParameter('statement', $statement)
            ->andWhere('s.refused = :refused')
            ->setParameter('refused', true)
            ->orderBy('m.createdAt', 'desc')
            ->addOrderBy('m.id', 'desc')
        ;

        return $this->buildIndexedResult($query->getQuery()->getResult());
    }

    public function findForeignModificationsIndexed(Statement $statement): array
    {
        $query = $this->createQueryBuilder('m')
            ->leftJoin('m.modificationStatements', 's')
            ->andWhere('s.statement != :statement')
            ->setParameter('statement', $statement)
            ->andWhere('s.refused = :refused')
            ->setParameter('refused', false)
            ->innerJoin('s.chosen', 'x')
            ->orderBy('m.createdAt', 'desc')
            ->addOrderBy('m.id', 'desc')
        ;

        return $this->buildIndexedResult($query->getQuery()->getResult());
    }

    /**
     * @param Modification[] $rows
     *
     * @return array<int, Modification[]>
     */
    private function buildIndexedResult(array $rows): array
    {
        $results = [];

        foreach ($rows as $modification) {
            $results[$modification->getParagraph()->getId()][] = $modification;
        }

        return $results;
    }
}
