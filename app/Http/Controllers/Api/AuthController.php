<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\PasswordReset;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $authorization = $req->header("authorization");

        if (!isset($authorization)) return response([
            "msg" => "Authorization is missing on header",
            "code" => 400
        ], 400);

        $explodeAuthorization = explode(" ", $authorization);
        if (count($explodeAuthorization) < 2) return response([
            "msg" => "Invalid header",
            "code" => 400
        ], 400);

        $credential = base64_decode($explodeAuthorization[1]);
        $explodeCredential = explode(":", $credential);
        if (count($explodeCredential) < 2) return response([
            "msg" => "Invalid credential",
            "code" => 400
        ], 400);

        $username = $explodeCredential[0];
        $password = $explodeCredential[1];

        if (!Auth::attempt(['email' => $username, 'password' => $password])) return response([
            "msg" => "Username/password is wrong",
            "code" => 200
        ], 200);

        $newToken = md5(Str::random(10));
        User::where("email", $username)->update([
            "api_token" => $newToken
        ]);

        $user = User::where("email", $username)->first();
        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "user" => $user
            ]
        ]);
    }
    
    public function register(Request $req)
    {
        $req->validate([
            "role_id" => "required|exists:roles,id",
            "name" => "required",
            "email" => "required|unique:users,email",
            "password" => "required"
        ]);

        User::insert([
            "role_id" => $req->role_id,
            "name" => $req->name,
            "email" => $req->email,
            "password" => bcrypt($req->password),
            "api_token" => md5(Str::random(10)),
        ]);

        $user = User::where("email", $req->email)->first();
        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "user" => $user
            ]
        ]);
    }

    public function resetPassword(Request $req)
    {
        $req->validate([
            "email" => "required|exists:users,email"
        ]);

        $email = $req->email;
        $user = User::where("email", $email)->first();

        $token = md5(Str::random(10));
        PasswordReset::insert([
            "email" => $email,
            "token" => $token,
            "expired_at" => now()->addMinutes(10),
            "created_at" => now()
        ]);

        $user->link = url("/reset_password", $token);
        $user->notify(new ResetPasswordNotification($user));

        return response([
            "msg" => "Password reset email sent",
            "code" => 200
        ], 200);
    }
}
