<?php

namespace App\Controller;

use App\Workspace\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BillingController extends AbstractController
{
    #[Route('/facturation', name: 'app_billing', methods: ['GET'])]
    public function index(WorkspaceRepository $workspace): Response
    {
        return $this->render('billing/index.html.twig', ['invoices' => $workspace->findInvoices()]);
    }
}
