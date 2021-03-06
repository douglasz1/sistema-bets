<?php

namespace Bets\Http\Controllers\Technical;


use Bets\Http\Controllers\Controller;
use Bets\Services\Admin\ExpensesService as Expenses;
use Bets\Services\Admin\UsersService as Users;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    private $expenses;
    private $users;

    public function __construct(Expenses $expenses, Users $users)
    {
        $this->expenses = $expenses;
        $this->users = $users;
    }

    public function index()
    {
        return view('technical.expenses.index');
    }

    public function sellers()
    {
        try {
            $sellers = $this->users->getAllSellers();

            return response()->json([
                'sellers' => $sellers
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao listar cambistas'
            ], 400);
        }
    }

    public function send(Request $request)
    {
        try {
            $data = $request->get('seller');

            $this->expenses->create($data);

            return response()->json([
                'message' => 'Gasto cadastrado com sucesso',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao cadastrar gasto'
            ], 400);
        }
    }
}
