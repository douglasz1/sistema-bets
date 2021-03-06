<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Http\Controllers\Controller;
use Bets\Services\Admin\UsersService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    public function index()
    {
        return view('technical.balance.index');
    }

    public function sellers()
    {
        try {
            $sellers = $this->usersService->getAllSellers();

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

            if ($data['balance'] <= $data['limit']) {
                $sellerData['balance'] = $data['balance'];
            } else {
                $sellerData['balance'] = $data['limit'];
            }

            if ($data['daily_limit'] <= $data['limit']) {
                $sellerData['daily_limit'] = $data['daily_limit'];
            } else {
                $sellerData['daily_limit'] = $data['limit'];
            }

            $seller = $this->usersService->update($sellerData, $data['id']);

            return response()->json([
                'message' => 'Saldo enviado com sucesso',
                'seller' => $seller
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao enviar saldo'
            ], 400);
        }
    }

    public function reset(Request $request)
    {
        try {
            $data = $request->get('seller');

            $sellerData['balance'] = $data['limit'];

            $seller = $this->usersService->update($sellerData, $data['id']);

            return response()->json([
                'message' => 'Saldo enviado com sucesso',
                'seller' => $seller
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao enviar saldo'
            ], 400);
        }
    }

    public function resetAll(Request $request)
    {
        try {
            $sellers = $this->usersService->getAllSellers();

            $force = $request->has('force') ? (bool)$request->get('force') : false;

            foreach ($sellers as $seller) {
                if ($force) {
                    $seller['balance'] = $seller->limit;
                    $seller->save();
                } elseif ($seller->balance > 0) {
                    $seller['balance'] = $seller->limit;
                    $seller->save();
                }
            }

            return response()->json([
                'message' => 'Saldo enviado com sucesso',
                'sellers' => $sellers
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao enviar saldo'
            ], 400);
        }
    }

    public function zeroToAll()
    {
        try {
            $this->usersService->zeroToAll();

            return response()->json([
                'message' => 'Saldo zerado com sucesso',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao zerar saldo'
            ], 400);
        }
    }
}
