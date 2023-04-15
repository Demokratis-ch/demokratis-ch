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
        $client->submit($form);
        $client->followRedirect();
        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('h2', 'Bundesbeschluss über das Stimm- und Wahlrecht ab 16 Jahren');

        // Free text before
        $before = $crawler->filter('#free_text_before_0');
        self::assertCount(1, $before);
        $crawler = $client->click($before->link());
        $this->assertSelectorTextContains('h2', 'Freitext');

        $form = $crawler->selectButton('Speichern')->form();
        $form['free_text[text]']->setValue('This is a free text');
        $crawler = $client->submit($form);
        $this->assertSelectorTextContains('#freetext_content_before_0', 'This is a free text');

        // Free text after
        $before = $crawler->filter('#free_text_after_0');
        self::assertCount(1, $before);

        $crawler = $client->click($before->link());
        $this->assertSelectorTextContains('h2', 'Freitext');

        $form = $crawler->selectButton('Speichern')->form();
        $form['free_text[text]']->setValue('This is a free text that comes after');
        $crawler = $client->submit($form);
        $this->assertSelectorTextContains('#freetext_content_after_0', 'This is a free text that comes after');

        // Edit free text
        $before_edit = $crawler->filter('#freetext_content_before_0 + div > a');
        $crawler = $client->click($before_edit->link());
        $form = $crawler->selectButton('Speichern')->form();
        $form['free_text[text]']->setValue('This is an updated free text');
        $crawler = $client->submit($form);
        $this->assertSelectorTextNotContains('#freetext_content_before_0', 'This is a free text');
        $this->assertSelectorTextContains('#freetext_content_before_0', 'This is an updated free text');

        // Delete free text
        $before_delete = $crawler->filter('#freetext_content_before_0 + div > a:nth-child(2)');
        $crawler = $client->click($before_delete->link());

        $deleted = $crawler->filter('#freetext_content_before_0');
        self::assertCount(0, $deleted);
    }
}
