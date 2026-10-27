<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class RegionState
{
    public function __construct(
        public string $state = 'full',
        public ?string $title = null,
        public ?string $message = null,
        public ?string $retryUrl = null,
    ) {}
}
