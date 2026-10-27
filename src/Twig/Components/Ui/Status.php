<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Status
{
    public function __construct(
        public string $code = 'unknown',
        public ?string $text = null,
    ) {}
}
