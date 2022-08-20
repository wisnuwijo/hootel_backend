<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function resetPassword($token)
    {
        $getToken = PasswordReset::where('token',$token)->first();
        if (!isset($getToken)) return abort(403, "Invalid token");

        $expiredDate = now()->subMinutes(10);

        $date1 = new \DateTime($getToken->expired_at);
        $date2 = new \DateTime($expiredDate);

        if ($date1 < $date2) return abort(403, "Token expired");
        
        return view('auth.reset_password', [
            "token" => $token
        ]);
    }

    public function changePassword(Request $req)
    {
        $req->validate([
            "password" => "required",
            "password_retyped" => "required"
        ]);

        if ($req->password != $req->password_retyped) return redirect()->back()->with("error", "Password tidak sama");
        
        $getEmail = PasswordReset::where("token", $req->token)->first();
        if (!isset($getEmail)) return redirect()->back()->with("error", "Token tidak valid");

        $email = $getEmail->email;
        User::where("email", $email)->update([
            "password" => bcrypt($req->password),
            "api_token" => md5(Str::random(10))
        ]);

        PasswordReset::where("token", $req->token)->delete();

        return view('auth.reset_password_succeed');
    }
}
