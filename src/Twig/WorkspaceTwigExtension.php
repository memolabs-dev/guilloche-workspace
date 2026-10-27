<?php

namespace App\Twig;

use App\Workspace\MissionFilter;
use App\Workspace\MissionStatus;
use App\Workspace\WorkspaceRepository;
use Twig\Attribute\AsTwigFilter;
use Twig\Attribute\AsTwigFunction;

/** Données transverses du shell et mise en forme française, sans dépendre de l'extension intl. */
final readonly class WorkspaceTwigExtension
{
    private const MONTHS = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];

    public function __construct(private WorkspaceRepository $workspace)
    {
    }

    #[AsTwigFunction('pending_missions_count')]
    public function pendingMissionsCount(): int
    {
        return \count($this->workspace->findMissions(new MissionFilter(status: MissionStatus::Action)));
    }

    /** 30 oct. 2026 */
    #[AsTwigFilter('short_date')]
    public function shortDate(\DateTimeInterface $date): string
    {
        return \sprintf('%d %s %s', $date->format('j'), self::MONTHS[(int) $date->format('n') - 1], $date->format('Y'));
    }

    /** 480,00 € */
    #[AsTwigFilter('euros')]
    public function euros(int $amountInCents): string
    {
        return number_format($amountInCents / 100, 2, ',', "\u{202F}").' €';
    }
}
