<?php

namespace Bets\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param array $nivel
     * @return mixed
     */
    public function handle($request, Closure $next, ...$nivel)
    {
        try {
            $usuario = JWTAuth::parseToken()->authenticate();

            if (!$usuario->active) {
                auth('api')->logout();
                return response()->json(['status' => 'O usuário não está ativo'], 401);
            }

            if (!in_array($usuario->roles()->first()->name, $nivel)) {
                return response()->json(['erro' => 'Você não tem permissão para acessar está rota'], 401);
            }

        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['erro' => 'O token é inválido'], 401);

            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['erro' => 'O token está expirado'], 401);
            }

            return response()->json(['erro' => 'O token de autorização não foi encontrado'], 401);
        }

        return $next($request);
    }
}
