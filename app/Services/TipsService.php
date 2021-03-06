<?php

namespace Bets\Services;


use Bets\Jobs\CalculateBetStatus;
use Bets\Models\Tip;
use Bets\Models\Result;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;

class TipsService
{
    /**
     * @var Tip
     */
    private $tip;
    /**
     * @var QuotationsService
     */
    private $quotationsService;

    public function __construct(Tip $tip, QuotationsService $quotationsService)
    {
        $this->tip = $tip;
        $this->quotationsService = $quotationsService;
    }

    /**
     * @param array $quotations
     * @param $bet
     * @param int $quotationModifier
     * @return float|int
     * @throws Exception
     */
    public function createMany(array $quotations, $bet, $quotationModifier = 0)
    {
        $dataAtual = now()->toDateTimeString();
        $quotationTotal = 1;
        $matchesIDs = [];

        $quotations = $this->quotationsService->searchChoices($quotations);
        $categoriasCotacao = categorias_cotacao('futebol');

        $tips = collect([]);

        foreach ($quotations as $choice) {

            if (now()->toDateTimeString() > $choice->match->match_date) {
                throw new Exception("A partida {$choice->match->matchName()} já se iniciou, por favor, remova para continuar.");
            }

            $matchesIDs[] = $choice->match->match_id;

            $categoria = $categoriasCotacao->first(function ($categoria) use ($choice) {
                $mercado = $categoria['mercado'] === $choice['bet_slug'];
                $palpite = $categoria['palpite'] === $choice['choice_slug'];
                return $mercado && $palpite;
            });

            $value = $choice->value;

            if (!is_null($categoria)) {
                $value = $choice->value + ($choice->value * ($quotationModifier + ($categoria['alterar_cotacao'] / 100)));
                $value = number_format($value, 2);
            }

            if ((float)$value < 1.01) {
                $value = 1.01;
            } elseif ((float)$value > 100) {
                $value = 100;
            }

            $quotationTotal = $quotationTotal * $value;

            $tips->push([
                'bet_id' => $bet->id,
                'match_id' => $choice->match->match_id,
                'match_date' => $choice->match->match_date,
                'bet_slug' => $choice->bet_slug,
                'bet_name' => $choice->bet_name,
                'choice_name' => $choice->choice_name,
                'choice_slug' => $choice->choice_slug,
                'value' => $value,
                'created_at' => $dataAtual,
                'updated_at' => $dataAtual,
            ]);

        }

        $this->tip->newQuery()->insert($tips->toArray());

        Result::query()->whereIn('match_id', $matchesIDs)->update(['have_tips' => true]);

        return $quotationTotal;
    }

    /**
     * @param array $palpites
     * @param $bet
     * @param int $quotationModifier
     * @return float|int
     * @throws \Throwable
     */
    public function createFromLiveBets(array $palpites, $bet, $quotationModifier = 0)
    {
        $cotacaoTotal = 1;
        $dataAtual = now()->toDateTimeString();

        $categoriasCotacao = categorias_cotacao('futebol');

        $timestamp = now()->timestamp;

        $tips = collect([]);

        foreach ($palpites as $palpite) {

            $buscarEvento = true;
            $partida = null;

            while ($buscarEvento) {
                $partida = app(LiveMatchesService::class)->aoVivo($palpite['match_id']);

                if (empty($partida)) {
                    sleep(1);
                    continue;
                }

                $buscarEvento = $partida['time']['atualizado_em'] < $timestamp;
            };

            throw_if($partida['time']['minutos'] >= 85, \Exception::class, "A partida {$palpite['match_name']} não está mais disponível");

            // busca a opção
            $cotacao = Arr::first($partida['odds'], function ($item) use ($palpite) {
                return $item['bet_slug'] === $palpite['bet_slug'] && $item['choice_slug'] === $palpite['choice_slug'];
            }, []);

            throw_if(empty($cotacao) || intval($cotacao['suspend']) !== 0 || intval($cotacao['value']) === 0, \Exception::class, "A cotação {$palpite['choice_name']} da partida {$palpite['match_name']} não está mais disponível");

            $categoria = $categoriasCotacao->first(function ($categoria) use ($cotacao) {
                $mercado = $categoria['mercado'] === $cotacao['bet_slug'];
                $palpite = $categoria['palpite'] === $cotacao['choice_slug'];
                return $mercado && $palpite;
            });

            $value = $cotacao['value'] + ($cotacao['value'] * ($quotationModifier + ($categoria['alterar_cotacao'] / 100)));

            if ((float)$value < 1) {
                $value = 1;
            } elseif ((float)$value > 100) {
                $value = 100;
            }

            $cotacaoTotal = $cotacaoTotal * $value;

            $dataPartida = Carbon::createFromFormat('d/m H:i', $palpite['match_date'])->toDateTimeString();

            $tips->push([
                'bet_id' => $bet->id,
                'match_id' => $palpite['match_id'],
                'match_date' => $dataPartida,
                'bet_slug' => $cotacao['bet_slug'],
                'bet_name' => $cotacao['bet_name'],
                'choice_name' => $cotacao['choice_name'],
                'choice_slug' => $cotacao['choice_slug'],
                'value' => $value,
                'created_at' => $dataAtual,
                'updated_at' => $dataAtual,
            ]);
        }

        $this->tip->newQuery()->insert($tips->toArray());

        if ($tips->count() > 0) {
            $partidasIDs = Arr::pluck($palpites, 'match_id');
            Result::query()->whereIn('match_id', $partidasIDs)->update(['have_tips' => true]);
        }

        return $cotacaoTotal;
    }

    public
    function cancel($id)
    {
        $tip = $this->tip->findOrFail($id);

        $tip->status = 'canceled';
        $tip->save();

        $tip->bet->quotation_total = $this->calculateQuotationTotal($tip->bet);
        $tip->bet->prize = $this->calculateBetPrize($tip->bet, $tip->bet->seller);
        $tip->bet->save();

        dispatch((new CalculateBetStatus($tip->bet))->onQueue('betStatus'));
        return $tip;
    }

    private
    function calculateBetPrize($bet, $seller)
    {
        /*
         * Checks if the bet prize is higher than
         * the maximum allowable prize for the seller
         */
        $prize = $bet->bet_value * $bet->quotation_total;

        if (!is_null($seller)) {
            $betMaxValue = $bet->bet_value * $seller->max_prize_multiplier;

            if ($prize > $betMaxValue && $betMaxValue < $seller->max_prize) {
                $prize = $betMaxValue;
            }

            return ($prize > $seller->max_prize) ? $seller->max_prize : $prize;
        }

        return $prize;
    }

    private
    function calculateQuotationTotal($bet)
    {
        $collection = $bet->tips->where('status', '<>', 'canceled')->pluck('value');

        $quotationTotal = $collection->reduce(function ($carry, $item) {
            return $carry * (float)$item;
        }, 1);

        return $quotationTotal;
    }

    public
    function update(array $data, $id)
    {
        if (isset($data['status']) && $data['status'] === 'canceled') {
            return $this->cancel($id);
        }

        $tip = $this->tip->findOrFail($id);

        $tip->fill($data);

        $tip->save();

        return $tip;
    }
}
