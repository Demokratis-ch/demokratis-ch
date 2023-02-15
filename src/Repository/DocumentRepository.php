<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 *
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function add(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findDocumentsWithoutFiles(): mixed
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.type = :proposal')
            ->setParameter('proposal', 'proposal')
            ->andWhere('d.filepath IS NULL')
            ->leftJoin('d.consultation', 'c')
            ->andWhere('c.status = :status')
            ->setParameter('status', 'ongoing')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFilesToDelete(): mixed
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.type = :proposal')
            ->setParameter('proposal', 'proposal')
            ->andWhere('d.filepath IS NOT NULL')
            ->andWhere('d.imported = :imported')
            ->setParameter('imported', 'paragraphed')
            ->getQuery()
            ->getResult()
        ;
    }
}
