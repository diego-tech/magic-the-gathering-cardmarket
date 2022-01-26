<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfNotAdminUser
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
        $response = ["status" => 0, "msg" => ""];

        $user = $request->user();

        if (!$user) {
            $response['msg'] = "Usuario No Existe";
            $response['status'] = 0;

            return response()->json($response, 500);
        } else {
            if($user->roles == "Administrador") {
                $response['msg'] = "No tienes los permisos suficientes";
                $response['status'] = 0;

                return response()->json($response, 500);
            } else {
                return $next($request);
            }
        }
    }
}