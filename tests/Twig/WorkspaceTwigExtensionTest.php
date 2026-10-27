<?php

namespace App\Tests\Twig;

use App\Twig\WorkspaceTwigExtension;
use App\Workspace\InMemoryWorkspaceRepository;
use PHPUnit\Framework\TestCase;

final class WorkspaceTwigExtensionTest extends TestCase
{
    private WorkspaceTwigExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new WorkspaceTwigExtension(new InMemoryWorkspaceRepository());
    }

    public function testShortDateInFrench(): void
    {
        self::assertSame('30 oct. 2026', $this->extension->shortDate(new \DateTimeImmutable('2026-10-30')));
        self::assertSame('1 août 2026', $this->extension->shortDate(new \DateTimeImmutable('2026-08-01')));
    }

    public function testEuros(): void
    {
        self::assertSame("480,00\u{202F}€", str_replace(' ', "\u{202F}", $this->extension->euros(48000)));
        self::assertSame("1\u{202F}250,50 €", $this->extension->euros(125050));
    }

    public function testPendingMissionsCount(): void
    {
        self::assertSame(2, $this->extension->pendingMissionsCount());
    }
}
