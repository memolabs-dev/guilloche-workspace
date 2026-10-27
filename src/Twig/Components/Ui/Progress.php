<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Progress
{
    public function __construct(
        public int $value = 0,
    ) {}
}
