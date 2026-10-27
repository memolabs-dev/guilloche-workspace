<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Field
{
    public function __construct(
        public ?string $label = null,
        public ?string $id = null,
        public ?string $help = null,
        public ?string $error = null,
        public bool $required = false,
    ) {}
}
