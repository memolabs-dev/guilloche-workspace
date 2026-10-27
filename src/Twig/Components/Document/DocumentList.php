<?php

namespace App\Twig\Components\Document;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(exposePublicProps: true)]
class DocumentList
{
    public function __construct(
        public array $documents = [],
        public ?array $libraryFilters = null,
    ) {}
}
