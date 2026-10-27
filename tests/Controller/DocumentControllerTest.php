<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DocumentControllerTest extends WebTestCase
{
    public function testLibraryListsEveryDocument(): void
    {
        $crawler = self::createClient()->request('GET', '/documents');

        self::assertResponseIsSuccessful();
        self::assertCount(5, $crawler->filter('.document-item--library'));
        self::assertSelectorExists('label[for="document-search"]');
    }

    public function testMissionFilter(): void
    {
        $crawler = self::createClient()->request('GET', '/documents?mission=2');

        self::assertSame(
            ['Lettre de mission', 'Présentation de votre entreprise'],
            $crawler->filter('.document-item__title')->each(static fn ($node): string => $node->text()),
        );
        self::assertSelectorExists('#document-mission option[value="2"][selected]');
    }

    public function testEmptyResults(): void
    {
        self::createClient()->request('GET', '/documents?mission=5');

        self::assertSelectorTextContains('.ui-region', 'Aucun document trouvé');
        self::assertSelectorNotExists('.document-item--library');
    }

    public function testUnknownMissionIsDroppedFromTheUrl(): void
    {
        self::createClient()->request('GET', '/documents?mission=999&q=synth%C3%A8se');

        self::assertResponseRedirects('/documents?q=synth%C3%A8se');
    }

    public function testDocumentPageKeepsTheLibraryFilters(): void
    {
        $client = self::createClient();
        $client->request('GET', '/documents/1?q=synth%C3%A8se&mission=1');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Synthèse de votre situation');
        self::assertSelectorExists('a.page-back[href="/documents?q=synth%C3%A8se&mission=1"]');

        $client->request('GET', '/documents/1?mission=999');
        self::assertSelectorExists('a.page-back[href="/documents"]');
    }

    /** @return iterable<array{string, int}> */
    public static function invalidRequests(): iterable
    {
        yield ['/documents?mission[]=2', 400];
        yield ['/documents?q[]=x', 400];
        yield ['/documents/1?mission[]=1', 400];
        yield ['/documents/999', 404];
    }

    #[DataProvider('invalidRequests')]
    public function testInvalidRequests(string $url, int $status): void
    {
        self::createClient()->request('GET', $url);

        self::assertResponseStatusCodeSame($status);
    }
}
