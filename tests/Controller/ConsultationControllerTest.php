<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\ConsultationStimmUndWahlrecht16JaehrigeFixtures;
use App\Tests\FunctionalTestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConsultationControllerTest extends WebTestCase
{
    use FunctionalTestHelperTrait;

    public function testIndexAction(): void
    {
        $client = self::setUpClientWithFixtures([new ConsultationStimmUndWahlrecht16JaehrigeFixtures()]);

        $crawler = $client->request('GET', '/consultations/all');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3 a', 'Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');

        $client->clickLink('Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');

        $client->clickLink('Vernehmlassungsvorlage');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#legal-texts h2', 'Bundesbeschluss über das Stimm- und Wahlrecht ab 16 Jahren');

        $client->clickLink('Dokumente');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#documents a', 'Anhang');

        $client->clickLink('Diskussion');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#discussions p', 'Testdiskussion');

        $client->clickLink('Medien');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#media p', 'Artikel in der Zeitung');

    }

}
