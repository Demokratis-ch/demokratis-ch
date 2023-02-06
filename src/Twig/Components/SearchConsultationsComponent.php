<?php

namespace App\Twig\Components;

use App\Repository\ConsultationRepository;
use App\Service\SearchConsultationsService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('search_consultations')]
class SearchConsultationsComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;

    private SearchConsultationsService $search;

    public function __construct(private ConsultationRepository $consultationRepository, SearchConsultationsService $search)
    {
        $this->search = $search;
    }

    public function getConsultations(): array
    {
        if (!$this->query) {
            return [];
        }

        return $this->search->searchConsultations($this->query);
    }
}
