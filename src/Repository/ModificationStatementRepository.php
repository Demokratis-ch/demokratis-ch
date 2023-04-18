<?php

namespace App\Repository;

use App\Entity\ChosenModification;
use App\Entity\ModificationStatement;
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

    /**
     * @param ChosenModification[] $chosenModifications
     *
     * @return array<int, ModificationStatement[]>
     */
    public function findPeers(Statement $statement, array $chosenModifications): array
    {
        $query = $this->createQueryBuilder('m')
            ->addSelect('s')
            ->andWhere('m.modification in (:modification)')
            ->setParameter('modification', array_map(fn (ChosenModification $chosen) => $chosen->getModification(), $chosenModifications))
            ->andWhere('m.statement != :statement')
            ->setParameter('statement', $statement)
            ->leftJoin('m.statement', 's')
            ->andWhere('s.public = :public')
            ->setParameter('public', true)
        ;

        /** @var ModificationStatement[] $rows */
        $rows = $query->getQuery()->getResult();

        $results = [];
        foreach ($rows as $modificationStatement) {
            $results[$modificationStatement->getModification()->getId()][] = $modificationStatement;
        }

        return $results;
    }
}
