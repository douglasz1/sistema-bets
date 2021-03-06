<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Controllers\Controller;
use Bets\Services\Admin\PaymentsService as Payments;
use Bets\Services\Admin\UsersService as Users;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * @var Payments
     */
    private $payments;
    /**
     * @var Users
     */
    private $users;

    public function __construct(Payments $payments, Users $users)
    {
        $this->payments = $payments;
        $this->users = $users;
    }

    public function index()
    {
        return view('admin.payments.index');
    }

    public function sellers()
    {
        try {
            $sellers = $this->users->setOnlyActives(false)->getAllSellers();

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

            $this->payments->create($data);

            return response()->json([
                'message' => 'Depósito cadastrado com sucesso',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao cadastrar depósito'
            ], 400);
        }
    }
}
