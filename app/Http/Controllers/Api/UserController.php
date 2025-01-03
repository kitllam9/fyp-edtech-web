<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
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
                    'email'
                ])
            );
        } else {
            return $this->failed(message: 'Unauthenticated.');
        }
    }
}
