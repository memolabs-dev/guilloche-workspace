<?php

namespace App\Controller;

use App\Workspace\DocumentFilter;
use App\Workspace\Mission;
use App\Workspace\MissionFilter;
use App\Workspace\MissionStatus;
use App\Workspace\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/missions', name: 'app_mission_')]
final class MissionController extends AbstractController
{
    public function __construct(private readonly WorkspaceRepository $workspace)
    {
    }

    /**
     * Liste filtrée et fiche de la mission sélectionnée, dans une même Turbo Frame :
     * l'URL porte les filtres et la sélection, la page fonctionne aussi sans JavaScript.
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $q = '',
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $status = '',
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $selected = '',
    ): Response {
        $filter = new MissionFilter($q, MissionStatus::tryFrom($status));
        $missions = $this->workspace->findMissions($filter);
        $selection = array_find($missions, static fn (Mission $mission): bool => (string) $mission->id === $selected);

        // Une sélection hors du filtre courant n'a pas d'URL canonique : on la retire.
        if ('' !== $selected && null === $selection) {
            return $this->redirectToRoute('app_mission_index', $filter->toQuery());
        }

        return $this->render('mission/index.html.twig', [
            'missions' => $missions,
            'filter' => $filter,
            'statuses' => MissionStatus::cases(),
            'selected' => $selection,
            'documents' => $selection ? $this->workspace->findDocuments(new DocumentFilter(missionId: $selection->id)) : [],
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(int $id): Response
    {
        $mission = $this->workspace->findMission($id) ?? throw $this->createNotFoundException('Mission introuvable.');

        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
            'documents' => $this->workspace->findDocuments(new DocumentFilter(missionId: $mission->id)),
        ]);
    }
}
