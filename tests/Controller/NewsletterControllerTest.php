<?php

namespace App\Test\Controller;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsletterControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private NewsletterRepository $repository;
    private string $path = '/newsletter/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Newsletter::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testSubscribe(): void
    {
        $crawler = $this->client->request('GET', '/newsletter/subscribe');

        self::assertResponseStatusCodeSame(200);
    }

    public function testSuccess(): void
    {
        $crawler = $this->client->request('GET', '/newsletter');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Erfolgreich für den Newsletter angemeldet');
    }
}
