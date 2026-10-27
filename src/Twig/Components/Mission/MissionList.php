<?php

namespace App\Twig\Components\Mission;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(exposePublicProps: true)]
class MissionList
{
    public function __construct(
        public array $missions = [],
        public ?string $query = null,
        public ?string $status = null,
        public ?int $selectedId = null,
    ) {}
}
