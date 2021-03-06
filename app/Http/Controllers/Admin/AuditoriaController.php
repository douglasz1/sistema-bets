<?php

namespace Bets\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class AuditoriaController extends Controller
{
    public function index()
    {
        return view('admin.auditoria.index');
    }

    public function listar(Request $request)
    {
        try {
            $dataInicial = $request->filled('dataInicial')
                ? $request->get('dataInicial') : now()->toDateString();

            $dataFinal = $request->filled('dataFinal')
                ? $request->get('dataFinal') : now()->toDateString();

            $tipo = $request->filled('tipo')
                ? $request->get('tipo') : 'usuario';

            $logs = Activity::query()
                ->where('log_name' , $tipo)
                ->where('description' , 'updated')
                ->whereDate('created_at', '>=', $dataInicial)
                ->whereDate('created_at', '<=', $dataFinal)
                ->with('causer', 'subject')
                ->get();

            return response()->json([
                'logs' => $logs,
            ]);

        } catch (\Throwable $exception) {
            return response()->json([
                'mensagem' => 'Nada encontrado',
                'erro' => $exception->getMessage()
            ], 404);
        }
    }
}
