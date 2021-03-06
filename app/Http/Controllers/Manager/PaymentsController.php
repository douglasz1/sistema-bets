<?php

namespace Bets\Http\Controllers\Manager;

use Bets\Services\Manager\PaymentsService as Payments;
use Bets\Services\Manager\UsersService as Users;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

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
        return view('manager.payments.index');
    }

    public function sellers()
    {
        try {
            $sellers = $this->users
                ->setManager(auth()->id())
                ->getAllSellers();

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
