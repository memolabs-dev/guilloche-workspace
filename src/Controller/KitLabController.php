<?php

namespace App\Controller;

use App\Workspace\DocumentFilter;
use App\Workspace\Mission;
use App\Workspace\MissionStatus;
use App\Workspace\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/kit-lab', name: 'app_kit_lab_')]
final class KitLabController extends AbstractController
{
    public function __construct(private readonly WorkspaceRepository $workspace)
    {
    }

    #[Route('', name: 'home', methods: ['GET'])]
    public function home(Request $request): Response
    {
        return $this->render('kit_lab/home.html.twig', [
            'missionCount' => count($this->workspace->findMissions()),
            'theme' => $this->theme($request),
        ]);
    }

    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(Request $request): Response
    {
        $missions = $this->workspace->findMissions();
        $documents = $this->workspace->findDocuments();
        $invoices = $this->workspace->findInvoices();
        $open = array_filter($missions, static fn (Mission $m): bool => MissionStatus::Done !== $m->status);
        $pending = array_filter($missions, static fn (Mission $m): bool => $m->isAwaitingClient());
        $recent = array_slice($documents, 0, 3);
        $activity = array_map(static fn ($d): array => [
            'date' => $d->addedOn->format('d/m/Y'),
            'title' => $d->title,
            'description' => $d->mission->reference,
        ], $recent);

        return $this->render('kit_lab/dashboard.html.twig', [
            'missions' => $missions,
            'openCount' => count($open),
            'pendingCount' => count($pending),
            'invoiceTotal' => (int) array_sum(array_map(static fn ($i): int => $i->amountInCents, $invoices)) / 100,
            'activity' => $activity,
            'theme' => $this->theme($request),
        ]);
    }

    #[Route('/mission/{id}', name: 'mission', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function mission(int $id, Request $request): Response
    {
        $mission = $this->workspace->findMission($id)
            ?? throw $this->createNotFoundException('Mission inconnue.');
        $documents = $this->workspace->findDocuments(new DocumentFilter(missionId: $mission->id));
        $items = [
            ['term' => 'Référence', 'description' => $mission->reference, 'serial' => true],
            ['term' => 'Catégorie', 'description' => $mission->category],
            ['term' => 'Responsable', 'description' => $mission->advisor],
            ['term' => 'Échéance', 'description' => $mission->dueOn->format('d/m/Y')],
            ['term' => 'Prochaine étape', 'description' => $mission->nextStep],
        ];
        $events = array_map(static fn ($d): array => [
            'date' => $d->addedOn->format('d/m/Y'),
            'title' => $d->title,
            'description' => $d->state->label(),
        ], $documents);

        return $this->render('kit_lab/mission.html.twig', [
            'mission' => $mission,
            'details' => $items,
            'events' => $events,
            'tone' => $this->tone($mission->status),
            'theme' => $this->theme($request),
        ]);
    }

    #[Route('/form', name: 'form', methods: ['GET'])]
    public function form(Request $request): Response
    {
        $missions = $this->workspace->findMissions();
        $query = $request->query->all();
        $values = ['title' => '', 'mission' => '', 'notes' => ''];
        foreach ($values as $key => $_) {
            $value = $query[$key] ?? '';
            if (!is_string($value)) {
                throw new BadRequestHttpException('Valeur non scalaire.');
            }
            $values[$key] = mb_substr(trim($value), 0, 'notes' === $key ? 400 : 80);
        }
        $submitted = isset($query['title']) || isset($query['mission']) || isset($query['notes']);
        $validMission = array_find($missions, static fn (Mission $m): bool => (string) $m->id === $values['mission']);
        $errors = [];
        if ($submitted) {
            if ('' === $values['title']) {
                $errors[] = 'Le titre est requis.';
            }
            if (null === $validMission) {
                $errors[] = 'Sélectionnez une mission existante.';
            }
        }

        return $this->render('kit_lab/form.html.twig', [
            'missions' => $missions,
            'options' => array_map(static fn (Mission $m): array => [
                'value' => (string) $m->id,
                'label' => $m->title,
            ], $missions),
            'values' => $values,
            'submitted' => $submitted,
            'errors' => $errors,
            'selectedMission' => $validMission,
            'theme' => $this->theme($request),
        ]);
    }

    private function theme(Request $request): string
    {
        $theme = $request->query->get('theme', 'light');

        return 'dark' === $theme ? 'dark' : 'light';
    }

    private function tone(MissionStatus $status): string
    {
        return match ($status) {
            MissionStatus::Done => 'success',
            MissionStatus::Action => 'warning',
            MissionStatus::InProgress => 'info',
        };
    }
}
