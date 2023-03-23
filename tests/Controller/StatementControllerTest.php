<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\ConsultationStimmUndWahlrecht16JaehrigeFixtures;
use App\Tests\FunctionalTestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatementControllerTest extends WebTestCase
{
    use FunctionalTestHelperTrait;

    public function testIndexAction(): void
    {
        $client = self::setUpClientWithFixtures([new ConsultationStimmUndWahlrecht16JaehrigeFixtures()]);
        self::logInAs('admin@test.com', $client);

        $client->request('GET', '/statements');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Stellungnahmen');
        $this->assertSelectorTextContains('#statements p', 'Meine Meinung');

        $client->clickLink('Meine Meinung');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');

        $crawler = $client->clickLink('Intro Text hinzufügen');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('label', 'Einführender Text');

        $form = $crawler->selectButton('Speichern')->form();
        $form['statement_intro[intro]'] = 'Lorem Ipsum';
        $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#intro', 'Lorem Ipsum');

        $client->clickLink('Stellungnahme als fertig markieren');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > span', 'admin@test.com');

        $client->clickLink('Stellungnahme verstecken');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > a:nth-child(2)', 'Stellungnahme veröffentlichen');

        $client->clickLink('Stellungnahme veröffentlichen');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > a:nth-child(2)', 'Stellungnahme verstecken');

        $client->clickLink('Für Beiträge Dritter öffnen');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > a:nth-child(3)', 'Keine Beiträge Dritter akzeptieren');

        $client->clickLink('Keine Beiträge Dritter akzeptieren');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > a:nth-child(3)', 'Für Beiträge Dritter öffnen');
    }
}
