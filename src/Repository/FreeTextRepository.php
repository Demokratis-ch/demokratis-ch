<?php

namespace App\Repository;

use App\Entity\FreeText;
use App\Entity\Statement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FreeText>
 *
 * @method FreeText|null find($id, $lockMode = null, $lockVersion = null)
 * @method FreeText|null findOneBy(array $criteria, array $orderBy = null)
 * @method FreeText[]    findAll()
 * @method FreeText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FreeTextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreeText::class);
    }

    public function save(FreeText $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FreeText $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<int, array<string, FreeText>>
     */
    public function findFreeTextsByStatementIndexed(Statement $statement): array
    {
        $freeTexts = $this->findBy(['statement' => $statement]);

        $indexed = [];
        foreach ($freeTexts as $freeText) {
            // defensive programming, because for some reason these properties are nullable
            if ($freeText->getStatement() === null || $freeText->getPosition() === null) {
                continue;
            }

            $id = $freeText->getParagraph()->getId();
            if (!isset($indexed[$id])) {
                $indexed[$id] = ['before' => [], 'after' => []];
            }

            $indexed[$id][$freeText->getPosition()][] = $freeText;
        }

        return $indexed;
    }
}
