<?php
namespace App\Controller;

use App\Demo\DemoData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ExplorationController extends AbstractController
{
    #[Route('/explorations', name: 'explorations', methods: ['GET'])]
    public function index(): Response { return $this->redirectToRoute('exploration_missions', ['direction'=>'priorites','selected'=>1]); }

    #[Route('/explorations/missions/{direction}', name: 'exploration_missions', requirements: ['direction'=>'atelier|priorites|panorama'], methods: ['GET'])]
    public function missions(string $direction, Request $request, DemoData $data): Response
    {
        $query = mb_substr(trim($request->query->getString('q')), 0, 100);
        $status = $request->query->getString('status');
        if (!in_array($status, ['', 'action', 'progress', 'done'], true)) $status = '';
        $missions = array_values(array_filter($data->missions(), static fn(array $m): bool =>
            ($status === '' || $m['status'] === $status) && ($query === '' || mb_stripos($m['title'].' '.$m['category'], $query) !== false)
        ));
        $selection = $request->query->getString('selected');
        $selected = null;
        foreach ($missions as $mission) if ((string) $mission['id'] === $selection) $selected = $mission;
        if ($selection !== '' && !$selected) return $this->redirectToRoute('exploration_missions', array_filter(['direction'=>$direction,'q'=>$query,'status'=>$status], static fn($value) => $value !== ''));
        $documents = $selected ? $data->documentsForMission($selected['id']) : [];
        return $this->render('explorations/missions.html.twig', compact('direction','query','status','missions','selected','documents'));
    }
}
