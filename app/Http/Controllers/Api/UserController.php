<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        return $this->success(
            data: $request->user()->only([
                'username',
                'email',
                'points',
                'interests',
                'badges'
            ])
        );
    }

    public function getRanking(Request $request)
    {
        $user = $request->user();
        if ($user->group_id !== null) {
            return $this->success(
                data: User::where('group_id', $user->group_id)->orderByDesc('points')->paginate(10)
            );
        }
        return $this->success(message: 'Not enough data...');
    }
}
