<?php

namespace App\Service;

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

    public function getConsultations(
        string $canton,
    ): array {
        $response = $this->client->request(
            'GET',
            $this->scrapeHost.$canton.'.json'
        );

        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }
}
