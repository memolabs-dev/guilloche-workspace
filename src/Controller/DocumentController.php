<?php

namespace App\Controller;

use App\Workspace\DocumentFilter;
use App\Workspace\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/documents', name: 'app_document_')]
final class DocumentController extends AbstractController
{
    public function __construct(private readonly WorkspaceRepository $workspace)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $q = '',
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $mission = '',
    ): Response {
        $filter = $this->filter($q, $mission);

        // Une mission inconnue n'a pas d'URL canonique : on la retire du filtre.
        if ('' !== $mission && null === $filter->missionId) {
            return $this->redirectToRoute('app_document_index', $filter->toQuery());
        }

        return $this->render('document/index.html.twig', [
            'documents' => $this->workspace->findDocuments($filter),
            'filter' => $filter,
            'missions' => $this->workspace->findMissions(),
        ]);
    }

    /** Le lien de retour conserve les filtres valides de la bibliothèque. */
    #[Route('/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(
        int $id,
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $q = '',
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $mission = '',
    ): Response {
        $document = $this->workspace->findDocument($id) ?? throw $this->createNotFoundException('Document introuvable.');

        return $this->render('document/show.html.twig', [
            'document' => $document,
            'libraryFilter' => $this->filter($q, $mission),
        ]);
    }

    private function filter(string $query, string $mission): DocumentFilter
    {
        $missionId = ctype_digit($mission) ? $this->workspace->findMission((int) $mission)?->id : null;

        return new DocumentFilter($query, $missionId);
    }
}
