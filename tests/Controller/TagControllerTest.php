<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\ConsultationStimmUndWahlrecht16JaehrigeFixtures;
use App\DataFixtures\TagFixtures;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Tests\FunctionalTestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TagControllerTest extends WebTestCase
{
    use FunctionalTestHelperTrait;

    public function testIndexAction(): void
    {
        $client = self::setUpClientWithFixtures([
            new ConsultationStimmUndWahlrecht16JaehrigeFixtures(),
            new TagFixtures(),
        ]);

        self::logInAs('test@test.com', $client);

        $client->request('GET', '/consultation/pa-iv-aktives-stimm-und-wahlrecht-fur-16-jahrige');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');

        $crawler = $client->clickLink('Tag hinzufügen');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Tags hinzufügen');

        $form = $crawler->selectButton('Speichern')->form();
        /** @var Tag[] $tags */
        $tag = self::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'bildung']);
        $form['add_tag[tags]']->setValue([$tag->getId()]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('#tags-bar a', 'Bildung');

        $client->clickLink('Bildung');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3 a', 'Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');

        $client->back();
        $this->assertResponseIsSuccessful();
        $client->clickLink('Tag hinzufügen');
        $this->assertResponseIsSuccessful();
        $crawler = $client->clickLink('neuen Tag');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Tag erstellen');

        $form = $crawler->selectButton('Speichern')->form();
        $form['create_tag[name]']->setValue('Foo');
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // suggested tags are not visible to normal users until they are approved. but to admins they are visible.
        $this->assertSelectorTextNotContains('#tags-bar > *:nth-child(3)', 'Foo');

        self::logInAs('admin@test.com', $client);
        $client->request('GET', '/consultation/pa-iv-aktives-stimm-und-wahlrecht-fur-16-jahrige');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');
        $this->assertSelectorTextContains('#tags-bar > *:nth-child(3)', 'Foo');
    }
}
