<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request) {
        $request->validated();

        if(!auth()->attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user = User::where('email', $request->email)->first();
        return $this->successResponse([
            'user' => $user,
            'token' => $user->createToken('authToken')->plainTextToken,
        ]
        );

    }

    public function register(RegisterUserRequest $request) {
        $request->validated();
        $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
        return $this->successResponse([
            'user' => $user,
            'token' => $user->createToken('authToken')->plainTextToken,
        ]
        );

    }

    public function logout() {
        Auth::user()->tokens()->delete();
        return $this->successResponse('Logged out successfully');
    }

}

