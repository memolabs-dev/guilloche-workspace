<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Dialog
{
    public function __construct(
        public ?string $id = null,
        public ?string $title = null,
    ) {}
}
