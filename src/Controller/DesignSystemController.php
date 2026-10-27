<?php

namespace App\Controller;

use App\Workspace\DocumentFilter;
use App\Workspace\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/** Vitrine du design system : les composants du produit, sur les mêmes fixtures. */
final class DesignSystemController extends AbstractController
{
    private const SECTIONS = ['overview', 'tokens', 'button', 'field', 'feedback', 'motion', 'pattern'];

    #[Route('/design-system', name: 'app_design_system', methods: ['GET'])]
    public function index(
        WorkspaceRepository $workspace,
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $section = 'overview',
    ): Response {
        return $this->render('design_system/index.html.twig', [
            'section' => \in_array($section, self::SECTIONS, true) ? $section : 'overview',
            'missions' => $workspace->findMissions(),
            'documents' => $workspace->findDocuments(new DocumentFilter(missionId: 1)),
        ]);
    }
}
