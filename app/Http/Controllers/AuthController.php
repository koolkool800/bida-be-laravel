<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user_name = $request->input('user_name');
        $password = $request->input('password');

        $user = User::where('user_name', $user_name)->first();
        if(!$user) {
            return response()->json(['error_code' =>  'AUTH_0', 'error' => 'Unauthorized'], 401);
        }
        if (!Hash::check($password, $user->password)) {
            return response()->json(['error_code' =>  'AUTH_0', 'error' => 'Unauthorized'], 401);
        }

        $payload = JWTFactory::sub($user->id)
        ->role($user->role)
        ->make();
        $token = JWTAuth::encode($payload);

        return response()->json([
            'message' => 'Successfully',
            'data' => [
                'access_token' => $token->get(),
                'token_type' => 'jwt',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'role' => $user->role,
                'name' => $user->name
            ]
        ]);
    }
}
