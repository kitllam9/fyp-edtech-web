<?php

namespace App\Enum;

enum BadgeType: string
{
    case POINTS = 'points';
    case NOTES = 'notes';
    case EXERCISE = 'exercise';
    case MIXED = 'mixed';
}
