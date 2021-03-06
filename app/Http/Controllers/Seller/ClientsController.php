<?php

namespace Bets\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;
use Bets\Models\Client;
use Bets\Models\Bet;

class ClientsController extends Controller
{
    public function index()
    {
        return view('seller.clients.index');
    }

    public function list()
    {
        $clients = Client::query()
            ->select('name', 'id')
            ->where('seller_id', auth()->id())
            ->orderBy('name')
            ->get();

        return response()->json([
            'clients' => $clients
        ]);
    }

    public function store(Request $request)
    {
        try {
            $client_check = Client::query()
                ->where([
                            ['name', '=', $request->get('name')],
                            ['seller_id', '=', auth()->id()]
                        ])
                ->first();

            if($client_check){
                throw new \Exception('Você já cadastrou um cliente com esse nome.', 401);
            }else{
                $client = Client::create([
                    'name' => $request->get('name'),
                    'seller_id' => auth()->id(),
                ]);

                return response()->json([
                    'client' => $client
                ]);
            }

        } catch (\Throwable $exception) {
            return response()->json([
                'choices' => [$exception->getMessage()],
            ], 400);
        }
    }

    public function destroy($clientId)
    {
        try {
            $bet = Bet::where('client_id', $clientId)
            ->whereDate('created_at', '>=', today()->subDays(7))
            ->first();
            if($bet){
                throw new \Exception('Não é possivel apagar o usuário', 401);
            }else{
                Client::where('id', $clientId)->delete();
                $clients = Client::query()
                    ->select('name', 'id')
                    ->where('seller_id', auth()->id())
                    ->get();

                return response()->json([
                    'clients' => $clients
                ]);
            }
        } catch (\Throwable $exception) {
            return response()->json([
                'result' => $exception->getMessage()
            ], $exception->getCode());
        }
    }
}
