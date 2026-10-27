<?php

namespace App\Workspace;

/** Accès en lecture à l'espace d'un dirigeant. Les contrôleurs ne connaissent que ce contrat. */
interface WorkspaceRepository
{
    /** @return list<Mission> */
    public function findMissions(MissionFilter $filter = new MissionFilter()): array;

    public function findMission(int $id): ?Mission;

    /** @return list<Document> */
    public function findDocuments(DocumentFilter $filter = new DocumentFilter()): array;

    public function findDocument(int $id): ?Document;

    /** @return list<Invoice> */
    public function findInvoices(): array;
}
