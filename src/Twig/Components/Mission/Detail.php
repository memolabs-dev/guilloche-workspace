<?php

namespace App\Twig\Components\Mission;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Detail
{
    public function __construct(
        public array $mission = [],
        public array $documents = [],
        public int $headingLevel = 2,
        public ?string $focusTarget = null,
    ) {}
}
