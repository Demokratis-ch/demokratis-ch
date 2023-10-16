<?php

namespace App\Service;

use App\Enums\Cantons;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FetchCantonalConsultation
{
    private HttpClientInterface $client;

    public function __construct(
        #[Autowire('%scrape_host%')]
        string $scrapeHost,
        HttpClientInterface $client,
    ) {
        $this->client = $client;
        $this->scrapeHost = $scrapeHost;
    }

    public function getAllCantonalConsultations()
    {
        foreach (Cantons::cases() as $canton) {
            $this->getConsultations($canton->value);
        }
    }

    public function getConsultations(
        string $canton,
    ) {
        $response = $this->client->request(
            'GET',
            $this->scrapeHost.$canton.'.json'
        );

        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }
}
