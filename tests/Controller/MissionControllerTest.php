<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MissionControllerTest extends WebTestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function listings(): iterable
    {
        yield 'all missions' => ['/missions', 'Rémunération du dirigeant'];
        yield 'selection shows the next step' => ['/missions?selected=1', 'Prochaine étape : valider la synthèse'];
        yield 'mission without documents' => ['/missions?selected=5', 'Aucun document associé'];
        yield 'unknown status is ignored' => ['/missions?status=invalide', 'Rémunération du dirigeant'];
        yield 'empty search' => ['/missions?q=introuvable', 'Aucune mission trouvée'];
    }

    #[DataProvider('listings')]
    public function testListingRendersInPageAndInFrame(string $url, string $expected): void
    {
        $client = self::createClient();

        foreach ([[], ['HTTP_TURBO_FRAME' => 'mission-results']] as $headers) {
            $client->request('GET', $url, server: $headers);
            self::assertResponseIsSuccessful();
            self::assertSelectorTextContains('turbo-frame#mission-results', $expected);
        }
    }

    public function testStatusFilterKeepsMatchingMissionsOnly(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/missions?status=done');

        self::assertSelectorTextContains('.results-summary', '1 mission');
        self::assertSame(['Bilan de votre accompagnement'], $crawler->filter('.mission-list__title')->each(static fn ($node): string => $node->text()));
    }

    public function testSelectionIsMarkedForTheFrameController(): void
    {
        self::createClient()->request('GET', '/missions?selected=1');

        self::assertSelectorExists('.mission-workspace[data-selected="1"]');
        self::assertSelectorExists('#mission-1-title[data-filtered-results-target="focus"]');
        self::assertSelectorExists('.mission-list__link[aria-current="true"][href="/missions?selected=1"]');
    }

    public function testSelectionOutsideTheFilterRedirectsToTheCanonicalUrl(): void
    {
        $client = self::createClient();

        $client->request('GET', '/missions?status=done&selected=1');
        self::assertResponseRedirects('/missions?status=done');

        $client->request('GET', '/missions?selected=999');
        self::assertResponseRedirects('/missions');
    }

    public function testSearchIsEscaped(): void
    {
        $client = self::createClient();
        $client->request('GET', '/missions?q=%3Cscript%3E');

        self::assertResponseIsSuccessful();
        self::assertStringNotContainsString('<script>alert', (string) $client->getResponse()->getContent());
        self::assertSelectorTextContains('.results-summary', '« <script> »');
    }

    /** @return iterable<int, array{string}> */
    public static function nonScalarParameters(): iterable
    {
        yield ['/missions?q[]=x'];
        yield ['/missions?status[]=x'];
        yield ['/missions?selected[]=1'];
    }

    #[DataProvider('nonScalarParameters')]
    public function testNonScalarParametersAreRejected(string $url): void
    {
        self::createClient()->request('GET', $url);

        self::assertResponseStatusCodeSame(400);
    }

    public function testShowPage(): void
    {
        $client = self::createClient();
        $client->request('GET', '/missions/1');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Rémunération du dirigeant');
        self::assertSelectorTextContains('.mission-sheet__serial', 'AR-2026-042');

        $client->request('GET', '/missions/999');
        self::assertResponseStatusCodeSame(404);
    }
}
