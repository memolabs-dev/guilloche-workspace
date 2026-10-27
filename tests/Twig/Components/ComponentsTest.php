<?php

namespace App\Tests\Twig\Components;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ComponentsTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testButtonMergesCallerAttributes(): void
    {
        $crawler = $this->renderTwigComponent('Ui:Button', ['class' => 'extra', 'data-action' => 'catalog#notify'], 'Continuer')->crawler();
        $button = $crawler->filter('button');

        self::assertSame('ui-button ui-button--primary ui-button--solid ui-button--md extra', $button->attr('class'));
        self::assertSame('catalog#notify', $button->attr('data-action'));
        self::assertSame('button', $button->attr('type'));
        self::assertNull($button->attr('disabled'));
    }

    public function testLoadingButtonIsDisabledAndBusy(): void
    {
        $button = $this->renderTwigComponent('Ui:Button', ['loading' => true], 'Envoi')->crawler()->filter('button');

        self::assertSame('true', $button->attr('aria-busy'));
        self::assertNotNull($button->attr('disabled'));
    }

    public function testButtonWithHrefIsALink(): void
    {
        $crawler = $this->renderTwigComponent('Ui:Button', ['href' => '/documents'], 'Ouvrir')->crawler();

        self::assertCount(1, $crawler->filter('a.ui-button[href="/documents"]'));
        self::assertCount(0, $crawler->filter('[disabled], [type]'));
    }

    public function testRosetteTracesItsProgress(): void
    {
        $svg = $this->renderTwigComponent('Ui:Guilloche', ['seed' => 1, 'progress' => 75, 'size' => 44])->crawler()->filter('svg');

        self::assertStringContainsString('--p: 75', (string) $svg->attr('style'));
        self::assertSame('75 100', $svg->filter('.ui-guilloche__trace')->attr('stroke-dasharray'));
        self::assertSame('true', $svg->attr('aria-hidden'));
    }

    public function testRosetteIsStableForAGivenSeed(): void
    {
        $first = $this->renderTwigComponent('Ui:Guilloche', ['seed' => 3])->crawler()->filter('.ui-guilloche__trace')->attr('d');
        $second = $this->renderTwigComponent('Ui:Guilloche', ['seed' => 3])->crawler()->filter('.ui-guilloche__trace')->attr('d');
        $other = $this->renderTwigComponent('Ui:Guilloche', ['seed' => 4])->crawler()->filter('.ui-guilloche__trace')->attr('d');

        self::assertSame($first, $second);
        self::assertNotSame($first, $other);
    }
}
