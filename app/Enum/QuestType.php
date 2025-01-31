<?php

namespace App\Enum;

enum QuestType: string
{
    case NOTES = 'notes';
    case EXERCISE = 'exercise';
    case PERCENTAGE = 'percentage';
    case MIXED = 'mixed';
}
