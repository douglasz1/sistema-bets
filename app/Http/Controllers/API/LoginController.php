<?php

namespace Bets\Http\Controllers\API;

use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required',
                'password' => 'required',
            ]);

            $credentials = $request->only('username', 'password');

            $credentials['active'] = true;

            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['erro' => 'erro ao fazer login'], 400);
            }
        } catch (\Throwable $e) {
            return response()->json(['erro' => 'não foi possível entrar no sistema'], 400);
        }

        $usuario = auth('api')->user();

        return response()->json(compact('token', 'usuario'));
    }

    public function perfil(Request $request)
    {
        $usuario = auth('api')->userOrFail();

        return response()->json(compact('usuario'));
    }
}
