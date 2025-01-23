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
        $user = $request->user();
        if ($user) {
            return $this->success(
                data: $user->only([
                    'username',
                    'email',
                    'points',
                    'interest'
                ])
            );
        } else {
            return $this->failed(message: 'Unauthenticated.');
        }
    }
}
