<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //validate request
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'tc' => 'required'
        ]);
        if (User::where('email', $request->email)->first()) {
            return response([
                "message" => "email already exists",
                "status" => "failed",
            ], 200);
        }
        //create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tc' => json_decode($request->tc)
        ]);
        //return data
        $token = $user->createToken($request->email)->plainTextToken;
        return response([
            'token' => $token,
            "message" => "successfully registered",
            "status" => "ok",
        ], 201);
    }

    //log in form
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required",
            "password" => "required"
        ]);
        //check email and password
        $user = User::where(["email" => $request->email])->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;
            return response([
                "message" => "Successful Login",
                "token" => $token,
                "status" => "Ok",
            ]);
        }
        return response([
            "message" => "invalid credentials",
            "status" => "Failed"
        ], 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            "message" => "Logged out successfully"
        ]);
    }
}
