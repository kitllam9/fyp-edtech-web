<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function updateUserBadges(Request $request)
    {
        // Update user points
        $user = $request->user();
        $user->update([
            'points' => $user->points + $request->input('points')
        ]);

        // Get unearned badges, then check if user has met the target points
        $badges = Badge::where('type', 'points')->whereNotIn('id', $user->badges)->get();
        $earnedBadgeIds = $user->$badges;
        $nextRequirement = 0;
        foreach ($badges as $badge) {
            if ($user->points < $badge->target) {
                $nextRequirement = $badge->target;
                break;
            }
            $earnedBadgeIds[] = $badge->id;
        }

        // Update user badges
        $user->update([
            'badges' => json_encode($earnedBadgeIds),
        ]);

        // Return the next requirement for displaying the progress bar on frontend
        return $this->success([
            'nextRequirement' => $nextRequirement
        ]);
    }
}
