<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use App\Models\Content;
use App\Models\History;
use App\Models\Quest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class QuestController extends Controller
{
    public function getQuestsWithStatus(Request $request)
    {
        $user = $request->user();

        $completedContentIds = History::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->pluck('content_id')
            ->toArray();

        $percentages = History::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->pluck('score');

        $quests = Quest::all()->map(function ($q) use ($user) {
            $q->progress = 0;
            $q->completed = false;
            if (!empty($user->finished_quests[$q->id])) {
                $q->claimed = Carbon::parse($user->finished_quests[$q->id])->isToday();
            }
            return $q;
        });

        foreach ($quests as $quest) {
            if ($quest->type == 'notes') {
                $completedNotesCount = Content::where('type', 'notes')
                    ->whereIn('id', $completedContentIds)
                    ->count();
                $quest->progress = $completedNotesCount;
                $quest->completed = $completedNotesCount >= $quest->target;
                continue;
            }
            if ($quest->type == 'exercise') {
                $completedExerciseCount = Content::where('type', 'exercise')
                    ->whereIn('id', $completedContentIds)
                    ->count();
                $quest->progress = $completedExerciseCount;
                $quest->completed = $completedExerciseCount >= $quest->target;
                continue;
            }
            if ($quest->type == 'mixed') {
                $completedExerciseCount = Content::whereIn('id', $completedContentIds)
                    ->count();
                $quest->progress = $completedExerciseCount;
                $quest->completed = $completedExerciseCount >= $quest->target;
                continue;
            }
            if ($quest->type == 'percentage') {
                $highestScore = 0;
                $passedScoreCount = $percentages->filter(function ($score) use ($quest, $highestScore) {
                    if ($score > $highestScore) {
                        $highestScore = $score;
                    }
                    return ($score * 100) > $quest->target;
                })->count();
                $requiredPassedScoreCount = $quest->multiple_percentage_amount ?? 1;
                $quest->progress = $quest->multiple_percentage_amount == null ? $highestScore : $passedScoreCount;
                $quest->completed = $passedScoreCount >= $requiredPassedScoreCount;
            }
        }

        return $this->success(data: [
            'quests' => $quests,
        ]);
    }

    public function complete(Request $request, int $id)
    {
        $user = $request->user();

        $records = $user->finished_quests ?? [];
        $records[$id] = Carbon::now();

        $user->update([
            'finished_quests' => $records,
        ]);

        return $this->success(message: 'Completed');
    }
}
