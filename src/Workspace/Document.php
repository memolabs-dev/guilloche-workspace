<?php

namespace App\Workspace;

final readonly class Document
{
    public function __construct(
        public int $id,
        public Mission $mission,
        public string $title,
        public string $category,
        public DocumentState $state,
        public \DateTimeImmutable $addedOn,
        public int $sizeInKb,
    ) {
    }
}
