<?php

namespace App\Service;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Persistence\ManagerRegistry;
use whatwedo\SearchBundle\Repository\IndexRepository;

class SearchConsultationsService
{
    private IndexRepository $indexRepository;
    private ManagerRegistry $doctrine;

    public function __construct(IndexRepository $indexRepository, ManagerRegistry $doctrine)
    {
        $this->indexRepository = $indexRepository;
        $this->doctrine = $doctrine;
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

        return array_map(function (array $result) use ($doctrine) {
            return $doctrine
                ->getRepository($result['model'])
                ->find($result['id'])
            ;
        }, $allIds);
    }
}
