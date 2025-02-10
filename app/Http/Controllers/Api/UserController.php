<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use App\Models\Recommendation;
use App\Models\Tag;
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

    public function update(Request $request)
    {
        $user = $request->user();

        $updates = [];

        if ($request->input('username')) {
            $updates['username'] = $request->input('username');
        }

        if ($request->input('email')) {
            $updates['email'] = $request->input('email');
        }

        if ($request->input('interests')) {
            $newInterests = json_decode($request->input('interests'));
            $updates['interests'] = $newInterests;

            $removed = array_diff($user->interests, $newInterests);
            $condition = collect($removed)->map(function ($name) {
                return "name LIKE '" . $name . "'";
            })->implode(' OR ');
            $tags = Tag::whereRaw($condition)->pluck('id');
            Recommendation::where('user_id', $user->id)->whereIn('product_id', $tags)->update(['score' => 0]);
        }

        $user->update($updates);

        return $this->success(message: 'Updated');
    }
}
