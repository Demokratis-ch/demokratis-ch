<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\BasicFixtures;
use App\DataFixtures\ConsultationStimmUndWahlrecht16JaehrigeFixtures;
use App\DataFixtures\RedirectFixtures;
use App\Tests\FunctionalTestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RedirectControllerTest extends WebTestCase
{
    use FunctionalTestHelperTrait;

    public function testRedirectHomeAction(): void
    {
        $client = self::setUpClientWithFixtures([
            new BasicFixtures(),
            new ConsultationStimmUndWahlrecht16JaehrigeFixtures(),
            new RedirectFixtures(),
        ]);

        self::logInAs('admin@test.com', $client);

        /* Access without token */
        $client->request('GET', '/', [], [], [
            'HTTP_HOST' => 'dmkr.at',
        ]);
        $this->assertResponseStatusCodeSame(404);

        /* Redirect to consultation */
        $client->request('GET', '/rc', [], [], [
            'HTTP_HOST' => 'dmkr.at',
        ]);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');

        /* Redirect to statement */
        $crawler = $client->request('GET', '/rs', [], [], [
            'HTTP_HOST' => 'dmkr.at',
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Geschützter Bereich');

        $form = $crawler->selectButton('Senden')->form();
        $form['redirect_password[password]'] = 'secret';
        $crawler = $client->submit($form);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /* Redirect to non-existing token */
        $crawler = $client->request('GET', '/none', [], [], [
            'HTTP_HOST' => 'dmkr.at',
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
}
