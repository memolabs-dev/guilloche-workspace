<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Badge
{
    public function __construct(
        public string $tone = 'neutral',
        public string $variant = 'solid',
    ) {}
}
