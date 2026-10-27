<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Skeleton
{
    public function __construct(
        public int $rows = 3,
    ) {}
}
