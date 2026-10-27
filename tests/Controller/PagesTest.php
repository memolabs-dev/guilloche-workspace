<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** Les pages sans paramètre se rendent et gardent leurs mentions de démonstration. */
final class PagesTest extends WebTestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function pages(): iterable
    {
        yield 'dashboard' => ['/', 'Bonjour Alexandre.'];
        yield 'billing' => ['/facturation', 'Aucun paiement à effectuer'];
        yield 'stack guide' => ['/comprendre-la-stack', 'Stimulus'];
    }

    #[DataProvider('pages')]
    public function testPageRenders(string $url, string $expected): void
    {
        self::createClient()->request('GET', $url);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('main', $expected);
        self::assertSelectorTextContains('.app-footer', 'données entièrement fictives');
    }

    public function testRailCountsMissionsAwaitingTheClient(): void
    {
        self::createClient()->request('GET', '/');

        self::assertSelectorTextContains('.rail__link[aria-current="page"]', 'Vue d’ensemble');
        self::assertSelectorTextContains('.rail__count', '2');
    }

    public function testInvoiceAmountsAreFormatted(): void
    {
        self::createClient()->request('GET', '/facturation');

        self::assertSelectorTextContains('.ledger__table tbody tr:first-child', 'F-2026-010');
        self::assertSelectorTextContains('.ledger__table tbody tr:first-child .is-amount', '480,00');
    }
}
