<?php
namespace App\Controller;

use App\Demo\DemoData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WorkspaceController extends AbstractController
{
    public function __construct(private readonly DemoData $data) {}

    #[Route('/', name: 'overview', methods: ['GET'])]
    public function overview(): Response
    {
        return $this->render('workspace/overview.html.twig', ['missions'=>$this->data->missions(),'documents'=>$this->data->documents()]);
    }

    #[Route('/missions', name: 'missions', methods: ['GET'])]
    public function missions(Request $request): Response
    {
        $query = mb_substr(trim($request->query->getString('q')), 0, 100);
        $status = $request->query->getString('status');
        if (!in_array($status, ['', 'action', 'progress', 'done'], true)) $status = '';
        $missions = array_values(array_filter($this->data->missions(), static fn(array $m): bool => ($status === '' || $m['status'] === $status) && ($query === '' || mb_stripos($m['title'].' '.$m['category'], $query) !== false)));
        $selection = $request->query->getString('selected');
        $selected = null;
        foreach ($missions as $mission) {
            if ((string) $mission['id'] === $selection) $selected = $mission;
        }
        if ($selection !== '' && !$selected) {
            return $this->redirectToRoute('missions', array_filter(['q'=>$query, 'status'=>$status], static fn($v) => $v !== ''));
        }
        $documents = $selected ? $this->data->documentsForMission($selected['id']) : [];
        return $this->render('workspace/missions.html.twig', compact('missions', 'query', 'status', 'selected', 'documents'));
    }

    #[Route('/missions/{id}', name: 'mission_detail', requirements: ['id'=>'\\d+'], methods: ['GET'])]
    public function mission(int $id): Response
    {
        $items = array_values(array_filter($this->data->missions(), static fn(array $m): bool => $m['id'] === $id));
        if (!$items) throw $this->createNotFoundException('Mission introuvable.');
        return $this->render('workspace/detail.html.twig', ['mission'=>$items[0], 'documents'=>$this->data->documentsForMission($id)]);
    }

    #[Route('/documents', name: 'documents', methods: ['GET'])]
    public function documents(Request $request): Response
    {
        ['query'=>$query, 'missionFilter'=>$missionFilter, 'invalidMission'=>$invalidMission] = $this->documentFilters($request);
        if ($invalidMission) {
            return $this->redirectToRoute('documents', array_filter(['q'=>$query], static fn($value) => $value !== ''));
        }
        $documents = array_values(array_filter($this->data->documents(), static fn(array $document): bool =>
            ($missionFilter === '' || (string) $document['mission_id'] === $missionFilter) &&
            ($query === '' || mb_stripos($document['title'].' '.$document['mission'].' '.$document['category'], $query) !== false)
        ));
        return $this->render('workspace/documents.html.twig', [
            'documents'=>$documents, 'query'=>$query, 'missionFilter'=>$missionFilter, 'missions'=>$this->data->missions(),
        ]);
    }

    #[Route('/documents/{id}', name: 'document_detail', requirements: ['id'=>'\\d+'], methods: ['GET'])]
    public function document(int $id, Request $request): Response
    {
        $items = array_values(array_filter($this->data->documents(), static fn(array $document): bool => $document['id'] === $id));
        if (!$items) throw $this->createNotFoundException('Document introuvable.');
        $filters = $this->documentFilters($request);
        return $this->render('workspace/document_detail.html.twig', [
            'document'=>$items[0], 'libraryFilters'=>array_filter(['q'=>$filters['query'], 'mission'=>$filters['missionFilter']], static fn($value) => $value !== ''),
        ]);
    }

    /** @return array{query: string, missionFilter: string, invalidMission: bool} */
    private function documentFilters(Request $request): array
    {
        $query = mb_substr(trim($request->query->getString('q')), 0, 100);
        $requested = $request->query->getString('mission');
        $allowed = array_map('strval', array_column($this->data->missions(), 'id'));
        $invalidMission = $requested !== '' && !in_array($requested, $allowed, true);
        return ['query'=>$query, 'missionFilter'=>$invalidMission ? '' : $requested, 'invalidMission'=>$invalidMission];
    }

    #[Route('/facturation', name: 'billing', methods: ['GET'])]
    public function billing(): Response { return $this->render('workspace/billing.html.twig', ['invoices'=>$this->data->invoices()]); }

    #[Route('/design-system', name: 'design_system', methods: ['GET'])]
    public function design(Request $request): Response
    {
        $section = $request->query->getString('section', 'overview');
        if (!in_array($section, ['overview','tokens','button','field','feedback','motion','pattern'], true)) $section = 'overview';
        return $this->render('workspace/design_system.html.twig', ['missions'=>$this->data->missions(), 'documents'=>$this->data->documentsForMission(1), 'section'=>$section]);
    }

    #[Route('/comprendre-la-stack', name: 'guide', methods: ['GET'])]
    public function guide(): Response { return $this->render('workspace/guide.html.twig'); }
}
