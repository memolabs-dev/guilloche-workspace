<?php

namespace App\Workspace;

final readonly class Mission
{
    public function __construct(
        public int $id,
        public string $reference,
        public string $title,
        public string $category,
        public MissionStatus $status,
        public string $nextStep,
        public int $progress,
        public \DateTimeImmutable $dueOn,
        public string $advisor,
    ) {
    }

    public function isAwaitingClient(): bool
    {
        return MissionStatus::Action === $this->status;
    }
}
