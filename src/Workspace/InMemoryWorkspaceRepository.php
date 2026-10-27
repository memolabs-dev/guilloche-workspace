<?php

namespace App\Workspace;

/**
 * Fixtures de démonstration pour une entreprise fictive. Aucune donnée réelle.
 * Une implémentation Doctrine ou API remplacerait cette classe sans toucher aux contrôleurs.
 */
final class InMemoryWorkspaceRepository implements WorkspaceRepository
{
    /** @var array<int, Mission> */
    private readonly array $missions;

    /** @var array<int, Document> */
    private readonly array $documents;

    public function __construct()
    {
        $missions = [
            new Mission(1, 'AR-2026-042', 'Rémunération du dirigeant', 'Rémunération', MissionStatus::Action, 'Valider la synthèse', 75, new \DateTimeImmutable('2026-10-30'), 'Camille Martin'),
            new Mission(2, 'AR-2026-038', 'Audit de votre structure', 'Structure', MissionStatus::InProgress, 'Analyse de votre dossier', 45, new \DateTimeImmutable('2026-11-04'), 'Camille Martin'),
            new Mission(3, 'AR-2026-045', 'Mise à jour des documents sociaux', 'Social', MissionStatus::Action, 'Ajouter deux pièces', 30, new \DateTimeImmutable('2026-11-06'), 'Julien Moreau'),
            new Mission(4, 'AR-2026-031', 'Bilan de votre accompagnement', 'Suivi', MissionStatus::Done, 'Synthèse disponible', 100, new \DateTimeImmutable('2026-10-22'), 'Camille Martin'),
            new Mission(5, 'AR-2026-047', 'Organisation du groupe', 'Structure', MissionStatus::InProgress, 'Premier échange', 20, new \DateTimeImmutable('2026-11-10'), 'Julien Moreau'),
        ];
        $this->missions = array_column(array_map(static fn (Mission $m): array => [$m->id, $m], $missions), 1, 0);

        $documents = [
            new Document(1, $this->missions[1], 'Synthèse de votre situation', 'Synthèse', DocumentState::ToReview, new \DateTimeImmutable('2026-10-26'), 248),
            new Document(2, $this->missions[2], 'Lettre de mission', 'Contrat', DocumentState::Validated, new \DateTimeImmutable('2026-10-24'), 186),
            new Document(3, $this->missions[3], 'Pièces nécessaires à votre dossier', 'Liste de pièces', DocumentState::ToComplete, new \DateTimeImmutable('2026-10-23'), 92),
            new Document(4, $this->missions[4], 'Compte rendu de rendez-vous', 'Compte rendu', DocumentState::Available, new \DateTimeImmutable('2026-10-22'), 134),
            new Document(5, $this->missions[2], 'Présentation de votre entreprise', 'Dossier', DocumentState::Available, new \DateTimeImmutable('2026-10-20'), 304),
        ];
        $this->documents = array_column(array_map(static fn (Document $d): array => [$d->id, $d], $documents), 1, 0);
    }

    public function findMissions(MissionFilter $filter = new MissionFilter()): array
    {
        return array_values(array_filter($this->missions, $filter->matches(...)));
    }

    public function findMission(int $id): ?Mission
    {
        return $this->missions[$id] ?? null;
    }

    public function findDocuments(DocumentFilter $filter = new DocumentFilter()): array
    {
        return array_values(array_filter($this->documents, $filter->matches(...)));
    }

    public function findDocument(int $id): ?Document
    {
        return $this->documents[$id] ?? null;
    }

    public function findInvoices(): array
    {
        return [
            new Invoice('F-2026-010', 'Accompagnement d’octobre', new \DateTimeImmutable('2026-10-01'), 48000, true),
            new Invoice('F-2026-009', 'Accompagnement de septembre', new \DateTimeImmutable('2026-09-01'), 48000, true),
            new Invoice('F-2026-008', 'Accompagnement d’août', new \DateTimeImmutable('2026-08-01'), 48000, true),
        ];
    }
}
