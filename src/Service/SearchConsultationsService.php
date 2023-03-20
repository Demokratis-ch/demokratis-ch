<?php

namespace App\Service;

use App\Entity\Consultation;
use App\Repository\ConsultationRepository;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Persistence\ManagerRegistry;
use whatwedo\SearchBundle\Repository\IndexRepository;

class SearchConsultationsService
{
    private IndexRepository $indexRepository;
    private ManagerRegistry $doctrine;

    public function __construct(IndexRepository $indexRepository, ManagerRegistry $doctrine, ConsultationRepository $consultationRepository)
    {
        $this->indexRepository = $indexRepository;
        $this->doctrine = $doctrine;
        $this->consultationRepository = $consultationRepository;
    }

    /**
     * @throws \ReflectionException
     * @throws AnnotationException
     */
    public function searchConsultations(string $query): array
    {
        $doctrine = $this->doctrine;

        // The search throws an exception if the query is empty
        if (!$query) {
            $query = '\n';
        }

        // search all entity classes and map them to their object
        $allIds = $this->indexRepository->searchEntities($query);

        $consultationIds = $this->indexRepository->search($query, Consultation::class);

        $consultations = $this->consultationRepository
            ->createQueryBuilder('c')
            ->where('c.id IN (:consultationIds)')
            ->setParameter('consultationIds', $consultationIds)
            ->andWhere('c.organisation IS NULL')
            ->getQuery()
            ->getResult()
        ;

        return $consultations;
    }
}
