<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GuideController extends AbstractController
{
    #[Route('/comprendre-la-stack', name: 'app_guide', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('guide/index.html.twig');
    }
}
