<?php

namespace App\Workspace;

final readonly class Invoice
{
    public function __construct(
        public string $number,
        public string $label,
        public \DateTimeImmutable $issuedOn,
        public int $amountInCents,
        public bool $paid,
    ) {
    }
}
