<?php

namespace Bets\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Bets\Http\Requests\BetsStoreRequest;
use Bets\Models\Client;
use Bets\Models\Bet;
use Bets\Models\Quotation;
use Bets\Services\BetsService;

class BetsController extends Controller
{
    /**
     * @var BetsService
     */
    private $betsService;

    public function __construct(BetsService $betsService)
    {
        $this->betsService = $betsService;
    }

    public function store(BetsStoreRequest $request)
    {
        try {
            $betId = $request->get('betId');
            $clientId = $request->get('name');

            $client = Client::find($clientId);
            $data['client_id'] = $clientId;
            $data['client_name'] = $client->name;

            $data['bet_value'] = moneyUS($request->get('bet_value'));
            $data['quotations'] = $request->get('choices');

            if (isset($betId)) {
                $bet = $this->betsService->validate($data, $betId);
            } else {
                $bet = $this->betsService->create($data);
            }

            $bet['print_url'] = route('bets.printing', ['id' => $bet->id]);
            $bet['bet_date'] = $bet->created_at->format("d/m H:i");

            $bet->load([
                'tips',
                'tips.match',
                'tips.match.league',
                'tips.match.league.country',
                'tips.match.sport',
                'seller' => function ($query) {
                    return $query->select('name', 'id', 'company_id', 'username', 'percentual_premio');
                },
                'seller.company' => function ($query) {
                    return $query->select('print_name AS name', 'id');
                }
            ]);

            $bet->tips->each(function ($item) {
                $item->match['human_date'] = $item->match->match_date->format("d/m H:i");
                $item->match['match_name'] = $item->match->matchName();
            });

            return response()->json(['bet' => $bet]);
        } catch (\Throwable $exception) {
            return response()->json([
                'choices' => [$exception->getMessage()],
            ], 400);
        }
    }

    public function printing($id)
    {
        try {
            $bet = $this->betsService->findById($id);

            return view('bets.ticket', compact('bet'));
        } catch (\Throwable $e) {
            logger()->error($e->getMessage());

            return redirect('/')->with('error', 'Erro ao encontrar simulação.');
        }
    }

    public function validateBet()
    {
        return view('bets.validate');
    }

    public function searchCode(Request $request)
    {
        $code = $request->has('code') ? $request->get('code') : '';

        try {
            $bet = $this->betsService->findByCode($code);

            $bet['bet_date'] = $bet->created_at->format('d/m/Y H:i');

            $seller = auth()->user();

            $quotationModifier = $seller->quotationModifier() + ($seller->company->quotation_modifier / 100);

            $quotationTotal = 1;

            $categoriasCotacao = categorias_cotacao('futebol');

            foreach ($bet->tips as $tip) {
                $tip->match['human_date'] = $tip->match->match_date->format('d/m H:i');
                $tip->match['match_name'] = $tip->match->matchName();

                $match = \Bets\Models\Match::query()
                    ->where('match_id', $tip->match_id)
                    ->first();

                $tip->match['id'] = $match->id;

                $odd = Quotation::query()
                    ->where('bet_slug', $tip->bet_slug)
                    ->where('choice_slug', $tip->choice_slug)
                    ->where('match_id', $match->id)
                    ->first();

                $tip['odd_id'] = $odd->id;

                $categoria = $categoriasCotacao->first(function ($categoria) use ($tip) {
                    $mercado = $categoria['mercado'] === $tip['bet_slug'];
                    $palpite = $categoria['palpite'] === $tip['choice_slug'];
                    return $mercado && $palpite;
                });

                $tempModifier = $quotationModifier + ($categoria['alterar_cotacao'] / 100);

                $tip->value = $odd->value + ($odd->value * $tempModifier);

                $tip->value = number_format($tip->value, 2);

                if ((float)$tip->value < 1.01) {
                    $tip->value = 1.01;
                } elseif ((float)$tip->value > 100) {
                    $tip->value = 100;
                }

                $quotationTotal = $quotationTotal * $tip->value;
            }

            $bet->quotation_total = $quotationTotal;

            $bet->prize = $this->betsService->calculateBetPrize($bet, auth()->user());

            $tips = $bet->tips->sortBy('match.match_date')->values()->all();
            unset($bet['tips']);
            $bet['tips'] = $tips;

            return response()->json(['bet' => $bet->toArray()]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'A simulação já foi validada'], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Erro! ' . $exception->getMessage()], 400);
        }
    }

    public function validateSave(Request $request)
    {
        try {
            $code = $request->has('code') ? $request->get('code') : '';

            $this->betsService->validate($code);

            return $this->printToJson($code);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Simulação não encontrada'], 400);
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function printToJson($id)
    {
        $bet = $this->betsService->findById($id);

        $bet['bet_date'] = $bet->created_at->format('d/m/Y H:i');
        $bet['print_url'] = route('bets.printing', ['id' => $bet->id]);

        $bet->tips->each(function ($item) {
            $item->match['human_date'] = $item->match->match_date->format("d/m/Y H:i");
            $item->match['match_name'] = $item->match->matchName();
        });

        $tips = $bet->tips->sortBy('match.match_date')->values()->all();
        unset($bet['tips']);
        $bet['tips'] = $tips;

        return response()->json(['bet' => $bet->toArray()]);
    }

    public function reprints()
    {
        return view('bets.reprint');
    }

    public function getReprints()
    {
        $sellerId = auth()->id();

        $bets = $this->betsService->getForReprint($sellerId);

        $currentDate = now();
        $cancelTime = config('app.cancel_time');

        $bets->each(function ($item) use ($currentDate, $cancelTime) {
            $item['can_cancel'] = $currentDate < $item->created_at->addMinutes($cancelTime);
        });

        return response()->json(['bets' => $bets]);
    }

    public function jsonToApp($id)
    {
        try {
            $bet = $this->betsService->findById($id);

            $bet['bet_date'] = $bet->created_at->format('d/m H:i');

            $bet->tips->each(function ($item) {
                $item->match['human_date'] = $item->match->match_date->format("d/m H:i");
                $item->match['match_name'] = $item->match->matchName();
            });

            $tips = $bet->tips->sortBy('match.match_date')->values()->all();
            unset($bet['tips']);
            $bet['tips'] = $tips;

            return response()->json(['bet' => $bet->toArray()]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Simulação não encontrada!'], 400);
        }
    }

    public function cancel($id)
    {
        try {
            $cancelTime = env('CANCEL_TIME', 0);

            if ($cancelTime <= 0) {
                throw new \Exception('Não é permitido cancelar apostas', 400);
            }

            $bet = $this->betsService->findById($id);

            if ($bet->seller_id !== auth()->id()) {
                throw new \Exception('Você não pode acessar essa aposta, pois ela pertence a outro cambista', 401);
            }

            $now = \Carbon\Carbon::now()->toDateTimeString();

            if ($bet->created_at->addMinutes($cancelTime) <= $now) {
                throw new \Exception('O tempo limite para cancelamento foi excedido');
            }

            foreach ($bet->tips as $tip) {
                if ($tip->match->match_date <= $now) {
                    $homeTeam = $tip->match->home_team;
                    $awayTeam = $tip->match->away_team;

                    throw new \Exception("A partida {$homeTeam} x {$awayTeam} já iniciou, não pode mais cancelar essa aposta.");
                }
            }

            $bet = $this->betsService->cancel($id);

            return response()->json([
                'bet' => $bet
            ]);

        } catch (\Throwable $exception) {
            $errorCode = $exception->getCode() ?: 400;
            return response()->json([
                'result' => $exception->getMessage()
            ], $errorCode);
        }
    }
}
