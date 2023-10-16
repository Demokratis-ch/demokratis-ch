<?php

namespace App\Repository;

use App\Entity\Consultation;
use App\Entity\Organisation;
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
    final public const PAGINATOR_PER_PAGE = 16;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function getPaginator(int $offset, string $filter = null, Tag $tag = null, Organisation $organisation = null): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('CASE WHEN c.startDate IS NULL THEN 0 ELSE 1 END', 'ASC')
            ->addOrderBy('c.startDate', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->leftJoin('c.tags', 't')
            ->addSelect('t')
        ;

        if ($filter && $filter !== 'all') {
            $query->andWhere('c.status = :val')
                ->setParameter('val', $filter);

            if ($filter === 'done') {
                $query->orWhere('c.status = :pendingReport')
                    ->setParameter('pendingReport', 'pending_report')
                    ->orWhere('c.status = :pendingAll')
                    ->setParameter('pendingAll', 'pending_statements_report')
                ;
            }
        }

        if ($tag) {
            $query
                ->andWhere('t.slug = :tag')
                ->setParameter('tag', $tag->getSlug())
            ;
        }

        if ($organisation) {
            $query->andWhere('c.organisation = :organisation')
                ->setParameter('organisation', $organisation);
        } else {
            $query->leftJoin('c.organisation', 'o')
                ->andWhere($query->expr()->in('o.type', ['federal', 'canton']));
        }

        return new Paginator($query->getQuery());
    }

    public function countByStatus(string|null $organisation = null): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.status, count(c.status) as count');

        if ($organisation) {
            $query->andWhere('c.organisation = :organisation')
                ->setParameter('organisation', $organisation);
        } else {
            $query->leftJoin('c.organisation', 'o')
                ->andWhere($query->expr()->in('o.type', ['federal', 'canton']));
        }

        $result = $query
            ->groupBy('c.status')
            ->getQuery()
            ->getArrayResult();

        return array_column($result, 'count', 'status');
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
