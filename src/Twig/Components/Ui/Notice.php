<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Notice
{
    public function __construct(
        public string $type = 'info',
        public ?string $message = null,
    ) {}
}
