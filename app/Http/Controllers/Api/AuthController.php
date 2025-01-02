<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->failed(errors: $validator->errors());
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->success(
            data: [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ],
            message: 'Register successful'
        );
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'     => 'required|string|max:255',
            'password'  => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->failed(errors: $validator->errors());
        }

        $credentials = $request->only('username', 'password');
        $user = User::where('username', $credentials['username'])->first();
        if (!($user && Hash::check($credentials['password'], $user->password))) {
            return $this->failed(
                errors: ['username' => ['Invalid credentials.']],
                statusCode: 401,
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(
            data: [
                'user' => $user,
                'access_token'  => $token,
                'token_type'    => 'Bearer'
            ],
            message: 'Login successful',
        );
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->success(
            message: 'Logout successful',
        );
    }
}
