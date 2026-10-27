<?php

namespace App\Workspace;

enum MissionStatus: string
{
    case Action = 'action';
    case InProgress = 'progress';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Action => 'À votre tour',
            self::InProgress => 'En cours',
            self::Done => 'Terminée',
        };
    }

    /** Teinte de l'encre qui confirme l'état (Ui:Status, rosettes). */
    public function tone(): string
    {
        return match ($this) {
            self::Action => 'amber',
            self::InProgress => 'blue',
            self::Done => 'green',
        };
    }
}
