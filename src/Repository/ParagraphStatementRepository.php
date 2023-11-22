<?php

namespace App\Repository;

use App\Entity\Paragraph;
use App\Entity\ParagraphStatement;
use App\Entity\Statement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParagraphStatement>
 *
 * @method ParagraphStatement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParagraphStatement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParagraphStatement[]    findAll()
 * @method ParagraphStatement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParagraphStatementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParagraphStatement::class);
    }

    public function save(ParagraphStatement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ParagraphStatement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOrCreate(Statement $statement, Paragraph $paragraph): ParagraphStatement
    {
        $ms = $this->findOneBy(['statement' => $statement, 'paragraph' => $paragraph]);
        if ($ms === null) {
            $ms = ParagraphStatement::create($statement, $paragraph);
            $this->getEntityManager()->persist($ms);
            $this->getEntityManager()->flush();
        }

        return $ms;
    }
}
