<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\ConsultationStimmUndWahlrecht16JaehrigeFixtures;
use App\Tests\FunctionalTestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModificationControllerTest extends WebTestCase
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

        $crawler = $client->clickLink('Meine Meinung');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');

        $modificationsInSidebar = $crawler->filter('.modifications')->first()->children('div');
        self::assertCount(1, $modificationsInSidebar);
        $commentsInSidebar = $crawler->filter('.comments')->first()->filter('.comment');
        self::assertCount(6, $commentsInSidebar);

        // new modification proposal

        $crawler = $client->click($crawler->filter('.paragraph')->first()->selectLink('Ändern')->link());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Änderungsvorschlag');

        $form = $crawler->selectButton('Speichern')->form();
        $form['modification[text]'] = $form['modification[text]']->getValue()."\n\n".'Hallihallo';
        $form['modification[justification]'] = 'Einfach so';
        $crawler = $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertStringNotContainsString('Hallihallo', $crawler->filter('.paragraph')->first()->text());

        $modificationsInSidebar = $crawler->filter('.modifications')->first()->children('div');
        self::assertCount(2, $modificationsInSidebar);
        $commentsInSidebar = $crawler->filter('.comments')->first()->children('.comment');
        self::assertCount(0, $commentsInSidebar);

        $crawler = $client->click($modificationsInSidebar->eq(1)->filter('a')->link());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Änderungsvorschlag');
        $this->assertSelectorTextContains('#diff', 'Hallihallo');
        $this->assertSelectorTextContains('#justification', 'Einfach so');

        $form = $crawler->selectButton('Übernehmen')->form();
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');
        $this->assertStringContainsString('Hallihallo', $crawler->filter('.paragraph')->first()->text());

        // reset to original paragraph

        $crawler = $client->clickLink('Original wiederherstellen');
        $this->assertResponseIsSuccessful();
        $this->assertStringNotContainsString('Hallihallo', $crawler->filter('.paragraph')->first()->text());

        $commentsInSidebar = $crawler->filter('.comments')->first()->filter('.comment');
        self::assertCount(1, $commentsInSidebar);

        // refuse the modification

        $crawler = $client->click($modificationsInSidebar->eq(1)->filter('a')->link());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Änderungsvorschlag');
        $this->assertSelectorTextContains('#diff', 'Hallihallo');
        $this->assertSelectorTextContains('#justification', 'Einfach so');

        $form = $crawler->selectButton('Ablehnen')->form();
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');

        $modificationsInSidebar = $crawler->filter('.modifications')->first()->children('div');
        self::assertCount(2, $modificationsInSidebar);
        $this->assertStringContainsString('Abgelehnt', $modificationsInSidebar->eq(1)->text());

        // reopen the modification

        $crawler = $client->click($modificationsInSidebar->eq(1)->filter('a')->link());
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Erneut öffnen');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meine Meinung');

        $modificationsInSidebar = $crawler->filter('.modifications')->first()->children('div');
        self::assertCount(2, $modificationsInSidebar);
        $this->assertStringNotContainsString('Abgelehnt', $modificationsInSidebar->eq(1)->text());

    }
}
