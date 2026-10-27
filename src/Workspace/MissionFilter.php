<?php

namespace App\Workspace;

/** Recherche et état saisis dans le filtre GET des missions. */
final readonly class MissionFilter
{
    public const QUERY_MAX_LENGTH = 100;

    public string $query;

    public function __construct(string $query = '', public ?MissionStatus $status = null)
    {
        $this->query = mb_substr(trim($query), 0, self::QUERY_MAX_LENGTH);
    }

    public function matches(Mission $mission): bool
    {
        return (null === $this->status || $mission->status === $this->status)
            && ('' === $this->query || false !== mb_stripos($mission->title.' '.$mission->category, $this->query));
    }

    /** @return array<string, string> paramètres d'URL non vides, pour reconstruire un lien filtré */
    public function toQuery(): array
    {
        return array_filter(['q' => $this->query, 'status' => $this->status?->value], static fn (?string $value): bool => null !== $value && '' !== $value);
    }
}
