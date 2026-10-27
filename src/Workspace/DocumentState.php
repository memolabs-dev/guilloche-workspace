<?php

namespace App\Workspace;

enum DocumentState: string
{
    case ToReview = 'to_review';
    case ToComplete = 'to_complete';
    case Validated = 'validated';
    case Available = 'available';

    public function label(): string
    {
        return match ($this) {
            self::ToReview => 'À consulter',
            self::ToComplete => 'À compléter',
            self::Validated => 'Validé',
            self::Available => 'Disponible',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::ToReview, self::ToComplete => 'amber',
            self::Validated, self::Available => 'green',
        };
    }
}
