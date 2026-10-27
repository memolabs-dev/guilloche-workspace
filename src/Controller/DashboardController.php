<?php

namespace App\Controller;

use App\Workspace\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(WorkspaceRepository $workspace): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'missions' => $workspace->findMissions(),
            'documents' => \array_slice($workspace->findDocuments(), 0, 3),
        ]);
    }
}
