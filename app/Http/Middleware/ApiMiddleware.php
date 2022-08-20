<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header("Authorization");
        
        $invalidCredentialResponse = [
            "msg" => "Invalid token",
            "code" => "403"
        ];

        if (is_null($token) || empty($token)) return response($invalidCredentialResponse);

        $splitToken = explode(" ", $token);
        if (count($splitToken) < 2) return response($invalidCredentialResponse);
        $apiToken = $splitToken[1];
        
        $user = User::where("api_token", $apiToken)->first();
        if (!isset($user)) return response($invalidCredentialResponse);

        $request->user = $user;

        return $next($request);
    }
}
