<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button
{
    public function __construct(
        public string $intent = 'primary',
        public string $appearance = 'solid',
        public string $size = 'md',
        public string $type = 'button',
        public bool $disabled = false,
        public bool $loading = false,
        public ?string $href = null,
    ) {}
}
