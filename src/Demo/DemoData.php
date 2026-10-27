<?php
namespace App\Demo;

final class DemoData
{
    public function missions(): array
    {
        return [
            ['id'=>1,'title'=>'Rémunération du dirigeant','category'=>'Rémunération','status'=>'action','label'=>'À votre tour','progress'=>75,'step'=>'Valider la synthèse','date'=>'30 oct. 2026','advisor'=>'Camille Martin','reference'=>'AR-2026-042'],
            ['id'=>2,'title'=>'Audit de votre structure','category'=>'Structure','status'=>'progress','label'=>'En cours','progress'=>45,'step'=>'Analyse de votre dossier','date'=>'4 nov. 2026','advisor'=>'Camille Martin','reference'=>'AR-2026-038'],
            ['id'=>3,'title'=>'Mise à jour des documents sociaux','category'=>'Social','status'=>'action','label'=>'À votre tour','progress'=>30,'step'=>'Ajouter deux pièces','date'=>'6 nov. 2026','advisor'=>'Julien Moreau','reference'=>'AR-2026-045'],
            ['id'=>4,'title'=>'Bilan de votre accompagnement','category'=>'Suivi','status'=>'done','label'=>'Terminée','progress'=>100,'step'=>'Synthèse disponible','date'=>'22 oct. 2026','advisor'=>'Camille Martin','reference'=>'AR-2026-031'],
            ['id'=>5,'title'=>'Organisation du groupe','category'=>'Structure','status'=>'progress','label'=>'En cours','progress'=>20,'step'=>'Premier échange','date'=>'10 nov. 2026','advisor'=>'Julien Moreau','reference'=>'AR-2026-047'],
        ];
    }

    public function documents(): array
    {
        return [
            ['id'=>1,'mission_id'=>1,'title'=>'Synthèse de votre situation','category'=>'Synthèse','mission'=>'Rémunération du dirigeant','date'=>'26 oct. 2026','size'=>'248 Ko','status'=>'action','label'=>'À consulter'],
            ['id'=>2,'mission_id'=>2,'title'=>'Lettre de mission','category'=>'Contrat','mission'=>'Audit de votre structure','date'=>'24 oct. 2026','size'=>'186 Ko','status'=>'done','label'=>'Validé'],
            ['id'=>3,'mission_id'=>3,'title'=>'Pièces nécessaires à votre dossier','category'=>'Liste de pièces','mission'=>'Mise à jour des documents sociaux','date'=>'23 oct. 2026','size'=>'92 Ko','status'=>'action','label'=>'À compléter'],
            ['id'=>4,'mission_id'=>4,'title'=>'Compte rendu de rendez-vous','category'=>'Compte rendu','mission'=>'Bilan de votre accompagnement','date'=>'22 oct. 2026','size'=>'134 Ko','status'=>'done','label'=>'Disponible'],
            ['id'=>5,'mission_id'=>2,'title'=>'Présentation de votre entreprise','category'=>'Dossier','mission'=>'Audit de votre structure','date'=>'20 oct. 2026','size'=>'304 Ko','status'=>'done','label'=>'Disponible'],
        ];
    }

    public function documentsForMission(int $id): array
    {
        return array_values(array_filter($this->documents(), static fn(array $document): bool => $document['mission_id'] === $id));
    }

    public function invoices(): array
    {
        return [
            ['id'=>'F-2026-010','title'=>'Accompagnement · octobre','date'=>'1 oct. 2026','amount'=>'480,00 €'],
            ['id'=>'F-2026-009','title'=>'Accompagnement · septembre','date'=>'1 sept. 2026','amount'=>'480,00 €'],
            ['id'=>'F-2026-008','title'=>'Accompagnement · août','date'=>'1 août 2026','amount'=>'480,00 €'],
        ];
    }
}
