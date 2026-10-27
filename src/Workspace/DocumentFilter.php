<?php

namespace App\Workspace;

/** Recherche et mission saisies dans le filtre GET de la bibliothèque. */
final readonly class DocumentFilter
{
    public string $query;

    public function __construct(string $query = '', public ?int $missionId = null)
    {
        $this->query = mb_substr(trim($query), 0, MissionFilter::QUERY_MAX_LENGTH);
    }

    public function matches(Document $document): bool
    {
        return (null === $this->missionId || $document->mission->id === $this->missionId)
            && ('' === $this->query || false !== mb_stripos($document->title.' '.$document->mission->title.' '.$document->category, $this->query));
    }

    /** @return array<string, string|int> */
    public function toQuery(): array
    {
        return array_filter(['q' => $this->query, 'mission' => $this->missionId], static fn (string|int|null $value): bool => null !== $value && '' !== $value);
    }
}
