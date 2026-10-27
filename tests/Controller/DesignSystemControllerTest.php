<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DesignSystemControllerTest extends WebTestCase
{
    /** @return iterable<array{string}> */
    public static function sections(): iterable
    {
        foreach (['overview', 'tokens', 'button', 'field', 'feedback', 'motion', 'pattern', 'unknown'] as $section) {
            yield $section => [$section];
        }
    }

    #[DataProvider('sections')]
    public function testEverySectionRenders(string $section): void
    {
        self::createClient()->request('GET', '/design-system?section='.$section);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.catalog-nav a[aria-current="page"]');
    }

    public function testNonScalarSectionIsRejected(): void
    {
        self::createClient()->request('GET', '/design-system?section[]=x');

        self::assertResponseStatusCodeSame(400);
    }

    public function testOverviewShowsComponentContracts(): void
    {
        self::createClient()->request('GET', '/design-system');

        self::assertSelectorTextContains('main', 'Couleurs & encres');
        self::assertSelectorExists('button.ui-button.catalog-merged[data-contract="merged"]:not([disabled])');
        self::assertSelectorExists('button.ui-button[aria-busy="true"][disabled]');
        self::assertSelectorExists('input#sample-error[aria-invalid="true"][aria-describedby="sample-error-help sample-error-error"]');
        self::assertSelectorExists('.ui-status--neutral');
        self::assertSelectorExists('article.mission-sheet li.document-item a[href="/documents/1"]');
        self::assertSelectorCount(1, '#mission-1-title');
    }
}
