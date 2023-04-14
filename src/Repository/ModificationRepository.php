<?php

namespace App\Repository;

use App\Entity\Modification;
use App\Entity\Paragraph;
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

    public function findOpenModifications(Paragraph $paragraph, Statement $statement): array
    {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.paragraph = :paragraph')
            ->setParameter('paragraph', $paragraph)
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

        return $query->getQuery()->getResult();
    }

    public function findRefusedModifications(Paragraph $paragraph, Statement $statement): array
    {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.paragraph = :paragraph')
            ->setParameter('paragraph', $paragraph)
            ->leftJoin('m.modificationStatements', 's')
            ->andWhere('s.statement = :statement')
            ->setParameter('statement', $statement)
            ->andWhere('s.refused = :refused')
            ->setParameter('refused', true)
            ->orderBy('m.createdAt', 'desc')
            ->addOrderBy('m.id', 'desc')
        ;

        return $query->getQuery()->getResult();
    }

    public function findForeignModifications(Paragraph $paragraph, Statement $statement): array
    {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.paragraph = :paragraph')
            ->setParameter('paragraph', $paragraph)
            ->leftJoin('m.modificationStatements', 's')
            ->andWhere('s.statement != :statement')
            ->setParameter('statement', $statement)
            ->andWhere('s.refused = :refused')
            ->setParameter('refused', false)
            ->innerJoin('s.chosen', 'x')
            ->orderBy('m.createdAt', 'desc')
            ->addOrderBy('m.id', 'desc')
        ;

        return $query->getQuery()->getResult();
    }
}
