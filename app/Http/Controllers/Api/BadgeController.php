<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use App\Models\History;
use App\Models\Content;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function getBadges(Request $request)
    {
        return $this->success(data: [
            'badges' => Badge::whereIn('id', json_decode($request->input('badge_ids') ?? '[]'))->get()
        ]);
    }
    public function checkUpdate(Request $request)
    {
        // Update user points
        $user = $request->user();
        $currentPoints = $user->points;
        $targetPoints = $user->points + (int) $request->input('points');
        $user->update([
            'points' => $targetPoints
        ]);

        // Get unearned badges, then check if user has met the target points
        $allUnearned = Badge::whereNotIn('id', $user->badges ?? []);
        $pointBadges = $allUnearned->where('type', 'points')
            ->get();
        
        $earnedBadgeIds = [];
        $targets = [];
        foreach ($pointBadges as $badge) {
            if ($user->points < $badge->target) {
                $targets[] = $badge->target;
                break;
            }
            $earnedBadgeIds[] = $badge->id;
            $targets[] = $badge->target;
        }

        // Update user badges
        $user->update([
            'badges' => array_merge($user->badge ?? [], $earnedBadgeIds),
        ]);

        // Return the next requirement for displaying the progress bar on frontend
        return $this->success([
            'targets' => $targets,
            'current_points' => $currentPoints,
            'target_points' => $targetPoints,
            'earned_badges' => Badge::whereIn('id', $earnedBadgeIds)->get(),
        ]);
    }
}
