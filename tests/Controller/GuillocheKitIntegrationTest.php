<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** Contrat d'intégration du paquet Guilloche, pas un simple test de compilation. */
final class GuillocheKitIntegrationTest extends WebTestCase
{
    public function testMissionListRendersDistinctStableGuillocheRosettesFromSeedOnly(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/missions');

        self::assertResponseIsSuccessful();
        $paths = $crawler->filter('.mission-list .ui-rosette[data-ui--rosette-ssr-value="true"] .ui-rosette__main')
            ->each(static fn ($node): string => (string) $node->attr('d'));

        self::assertNotEmpty($paths, 'Mission list must use the kit recipe with automatic server geometry');
        self::assertCount(count($paths), array_unique($paths), 'Mission references must have distinct paths');
        foreach ($paths as $path) {
            self::assertGreaterThan(1000, strlen($path));
            self::assertNotSame('M50 6a44 44 0 1 1-.1 0z', $path);
        }

        $again = $client->request('GET', '/missions');
        $pathsAgain = $again->filter('.mission-list .ui-rosette__main')
            ->each(static fn ($node): string => (string) $node->attr('d'));
        self::assertSame($paths, $pathsAgain, 'The same mission must keep its contour across requests');
    }
}
