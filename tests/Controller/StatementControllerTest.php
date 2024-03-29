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
        $client->followRedirects();
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

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#intro', 'Lorem Ipsum');

        $client->clickLink('Stellungnahme als fertig markieren');
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > span', 'Admin Istrator');

        $client->clickLink('Stellungnahme verstecken');
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > a:nth-child(2)', 'Stellungnahme veröffentlichen');

        $client->clickLink('Stellungnahme veröffentlichen');
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > a:nth-child(2)', 'Stellungnahme verstecken');

        $client->clickLink('Für Beiträge Dritter öffnen');
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > a:nth-child(3)', 'Keine Beiträge Dritter akzeptieren');

        $client->clickLink('Keine Beiträge Dritter akzeptieren');
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertSelectorTextContains('#actions > a:nth-child(3)', 'Für Beiträge Dritter öffnen');

        // Foreign statement

        $client->clickLink('Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');
        $this->assertSelectorTextContains('h1', 'Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');
        $client->clickLink('Fremde Meinung');
        $this->assertSelectorTextContains('h1', 'Fremde Meinung');
        $this->assertSelectorTextContains('.inspirations h3', 'Änderungsvorschläge aus anderen Stellungnahmen');

        $crawler = $client->clickLink('test@test.com');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Änderungsvorschlag');
        $this->assertSelectorTextContains('h2', 'Vorschlag von test@test.com');

        $form = $crawler->selectButton('Übernehmen')->form();
        $form['accept_refuse[reason]']->setValue('This is the reason');
        $client->submit($form);

        $this->assertSelectorTextContains('h1', 'Fremde Meinung');
        $this->assertSelectorTextContains('.related-statement', 'Meine Meinung');

        $crawler = $client->clickLink('Änderung vorschlagen');
        $form = $crawler->selectButton('Speichern')->form();
        $form['modification[text]']->setValue('A completely different text');
        $crawler = $client->submit($form);

        $link = $crawler->filter('.modifications')->selectLink('Admin Istrator')->link();
        $crawler = $client->click($link);
        $form = $crawler->selectButton('Übernehmen')->form();
        $form['accept_refuse[reason]']->setValue('This is another reason');
        $crawler = $client->submit($form);

        $this->assertSelectorTextContains('h2', 'Bundesbeschluss über das Stimm- und Wahlrecht ab 16 Jahren');

        // Free text before
        $crawler = $client->click($crawler->filter('#paragraph-1')->selectLink('Text hinzufügen')->first()->link());
        $this->assertSelectorTextContains('h2', 'Freitext');

        $form = $crawler->selectButton('Speichern')->form();
        $form['free_text[text]']->setValue('This is a free text');
        $crawler = $client->submit($form);
        $this->assertSelectorTextContains('#paragraph-1 .freetext-before', 'This is a free text');

        // Free text after
        $crawler = $client->click($crawler->filter('.paragraph')->first()->selectLink('Text hinzufügen')->eq(1)->link());
        $this->assertSelectorTextContains('h2', 'Freitext');

        $form = $crawler->selectButton('Speichern')->form();
        $form['free_text[text]']->setValue('This is a free text that comes after');
        $crawler = $client->submit($form);
        $this->assertSelectorTextContains('#paragraph-1 .freetext-after', 'This is a free text that comes after');

        // Edit free text
        $before_edit = $crawler->filter('#paragraph-1 .freetext-before')->selectLink('Bearbeiten')->first();
        $crawler = $client->click($before_edit->link());
        $form = $crawler->selectButton('Speichern')->form();
        $form['free_text[text]']->setValue('This is an updated free text');
        $crawler = $client->submit($form);
        $this->assertSelectorTextNotContains('#paragraph-1 .freetext-before', 'This is a free text');
        $this->assertSelectorTextContains('#paragraph-1 .freetext-before', 'This is an updated free text');

        // Delete free text
        $before_delete = $crawler->filter('#paragraph-1 .freetext-before')->selectLink('Entfernen')->first();
        $crawler = $client->click($before_delete->link());

        $deleted = $crawler->filter('#paragraph-1 .freetext-before');
        self::assertCount(0, $deleted);
    }
}
