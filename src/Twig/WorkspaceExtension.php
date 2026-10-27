<?php

namespace App\Twig;

use App\Demo\DemoData;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/** Données transverses du shell, lues dans les mêmes fixtures que les pages. */
final class WorkspaceExtension extends AbstractExtension
{
    public function __construct(private readonly DemoData $data) {}

    public function getFunctions(): array
    {
        return [new TwigFunction('pending_missions', $this->pendingMissions(...))];
    }

    public function pendingMissions(): int
    {
        return count(array_filter($this->data->missions(), static fn(array $m): bool => 'action' === $m['status']));
    }
}
