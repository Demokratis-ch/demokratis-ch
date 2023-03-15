<?php

namespace App\Repository;

use App\Entity\Consultation;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Consultation>
 *
 * @method Consultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consultation[]    findAll()
 * @method Consultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultationRepository extends ServiceEntityRepository
{
    /**
     * @var int
     */
    final public const PAGINATOR_PER_PAGE = 8;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function getPaginator(int $offset, string $filter = null, Tag $tag = null): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.organisation IS NULL')
            ->orderBy('c.startDate', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
        ;

        if ($filter && $filter !== 'all') {
            $query->andWhere('c.status = :val')
                ->setParameter('val', $filter);
        }

        if ($tag) {
            $query->leftJoin('c.tags', 't')
                ->andWhere('t.slug = :tag')
                ->setParameter('tag', $tag->getSlug())
            ;
        }

        return new Paginator($query->getQuery());
    }

    public function count($status = null)
    {
        $query = $this->createQueryBuilder('c');

        if ($status !== null) {
            $query->andWhere('c.status = :status')
                ->setParameter('status', $status)
                ->andWhere('c.organisation IS NULL')
            ;
        }

        return $query->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function add(Consultation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Consultation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
