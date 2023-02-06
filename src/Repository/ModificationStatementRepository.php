<?php

namespace App\Repository;

use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Paragraph;
use App\Entity\Statement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModificationStatement>
 *
 * @method ModificationStatement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModificationStatement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModificationStatement[]    findAll()
 * @method ModificationStatement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModificationStatementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModificationStatement::class);
    }

    public function save(ModificationStatement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ModificationStatement $entity, bool $flush = false): void
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
            ->leftJoin('m.chosenModifications', 'c')
            ->andWhere('c.chosenAt IS NULL')
        ;

        return $query->getQuery()->getResult();
    }

    public function findPeers(Modification $modification, Statement $statement): array
    {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.modification = :modification')
            ->setParameter('modification', $modification)
            ->andWhere('m.statement != :statement')
            ->setParameter('statement', $statement)
            ->leftJoin('m.statement', 's')
            ->andWhere('s.public = :public')
            ->setParameter('public', true)
        ;

        return $query->getQuery()->getResult();
    }
}
