<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Input
{
    public function __construct(
        public string $type = 'text',
        public ?string $id = null,
        public ?string $name = null,
        public ?string $placeholder = null,
        public ?string $value = null,
        public ?string $describedBy = null,
        public bool $disabled = false,
        public bool $invalid = false,
    ) {}
}
