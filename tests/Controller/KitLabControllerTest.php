<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class KitLabControllerTest extends WebTestCase
{
    public function testAllIntegrationPagesRenderActualKitComponents(): void
    {
        $client = self::createClient();
        $routes = [
            '/kit-lab' => 'Trois compositions',
            '/kit-lab/dashboard' => 'Pilotage des missions',
            '/kit-lab/mission/1' => 'Rémunération du dirigeant',
            '/kit-lab/form' => 'Préparer une mission',
        ];
        foreach ($routes as $path => $headline) {
            $crawler = $client->request('GET', $path);
            self::assertResponseIsSuccessful();
            self::assertStringContainsString($headline, $crawler->filter('h1')->text());
            self::assertCount(1, $crawler->filter('body.kit-lab'));
            self::assertCount(0, $crawler->filter('.app-shell'));
        }
    }

    public function testDashboardUsesActualWorkspaceFixtures(): void
    {
        $crawler = self::createClient()->request('GET', '/kit-lab/dashboard');
        self::assertResponseIsSuccessful();
        self::assertCount(3, $crawler->filter('.gp__stats .ui-stat'));
        self::assertCount(5, $crawler->filter('.gp__columns .ui-table tbody tr'));
        self::assertCount(3, $crawler->filter('.gp__columns .ui-timeline__item'));
        self::assertGreaterThanOrEqual(1, $crawler->filter('a[href="/kit-lab/mission/1?theme=light"]')->count());
        self::assertStringContainsString('Rémunération du dirigeant', $crawler->filter('.gp__columns .ui-table')->text());
        self::assertStringContainsString('1 440,00', $crawler->filter('.gp__stats')->text());
    }

    public function testMissionRouteChangesDataAndFiltersRelatedDocuments(): void
    {
        $client = self::createClient();
        $one = $client->request('GET', '/kit-lab/mission/1');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('AR-2026-042', $one->filter('.gp__breadcrumb')->text());
        self::assertStringContainsString('Synthèse de votre situation', $one->filter('.gp__side-column')->text());
        $two = $client->request('GET', '/kit-lab/mission/2');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Audit de votre structure', $two->filter('h1')->text());
        self::assertStringContainsString('Lettre de mission', $two->filter('.gp__side-column')->text());
        self::assertStringNotContainsString('Synthèse de votre situation', $two->filter('.gp__side-column')->text());

        $client->request('GET', '/kit-lab/mission/999999');
        self::assertResponseStatusCodeSame(404);
        $client->request('POST', '/kit-lab/mission/1');
        self::assertResponseStatusCodeSame(405);
    }

    public function testFormValidatesGetAndNeverSavesAnything(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/kit-lab/form');
        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('form[method=get]'));
        self::assertCount(1, $crawler->filter('select#kit-mission option[value="1"]'));
        self::assertCount(0, $crawler->filter('.kit-lab__result'));

        $crawler = $client->request('GET', '/kit-lab/form?title=Audit%20pilote&mission=1&notes=Relecture');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Aperçu confirmé', $crawler->filter('.kit-lab__result')->text());
        self::assertStringContainsString('Rémunération du dirigeant', $crawler->filter('.kit-lab__result')->text());
        self::assertStringContainsString('Audit pilote', $crawler->filter('.kit-lab__result')->text());

        $crawler = $client->request('GET', '/kit-lab/form?title=&mission=999');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Le titre est requis', $crawler->filter('.kit-lab__result')->text());

        $client->request('POST', '/kit-lab/form', ['title' => 'Impossible']);
        self::assertResponseStatusCodeSame(405);

        $crawler = $client->request('GET', '/kit-lab/dashboard');
        self::assertResponseIsSuccessful();
        self::assertCount(5, $crawler->filter('.gp__columns .ui-table tbody tr'));
    }

    public function testInvalidScalarsAndScriptInjectionAreSafe(): void
    {
        $client = self::createClient();
        $client->request('GET', '/kit-lab/form?title%5B%5D=test&mission=1');
        self::assertResponseStatusCodeSame(400);

        $crawler = $client->request('GET', '/kit-lab/form?title=%3Cscript%3Ealert(1)%3C%2Fscript%3E&mission=1&notes=%3Cimg%20src=x%20onerror=alert(1)%3E');
        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('.kit-lab__result script, .kit-lab__result img'));
        self::assertStringContainsString('<script>alert(1)</script>', $crawler->filter('.kit-lab__result')->text());

        $crawler = $client->request('GET', '/kit-lab/dashboard?theme=dark');
        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('html[data-ui-theme=dark]'));
    }

    public function testOriginalWorkspaceRemainsAvailableAndIndependent(): void
    {
        $client = self::createClient();
        $client->request('GET', '/');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.app-shell');
        self::assertSelectorNotExists('.kit-lab');
        $client->request('GET', '/missions');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.mission-list .ui-rosette');
    }
}
