<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Icon
{
    public function __construct(
        public string $iconName = 'file',
        public ?string $class = null,
        public string $size = '20',
    ) {}
}
