<?php

namespace Bets\Services;


use Bets\Models\Match;

class QuotationsPopulateService
{
    /**
     * @var Match
     */
    private $match;

    /**
     * @param array $quotations
     * @param Match $match
     * @throws \Exception
     */
    public function createFromJson(array $quotations, Match $match)
    {
        $cotacoes = collect([]);

        $this->match = $match->load('quotations');

        $categoriasCotacao = categorias_cotacao('futebol');

        $categoriasAntigas = cache()->tags('quotationNames')
            ->rememberForever('quotationNames-soccer', function () {
                return \Bets\Models\QuotationCategory::query()
                    ->where('sport_id', 1)
                    ->where('active', true)
                    ->get();
            });

        foreach ($quotations as $choice) {
            $categoria = $categoriasCotacao->first(function ($categoria) use ($choice) {
                // if (in_array($choice['bet_slug'], ['jogador-que-marca-qualquer-momento', 'jogador-que-marca-ultimo-gol', 'jogador-que-marca-1o-gol'])) {
                //     return true;
                // }

                $mercado = $categoria['mercado'] === $choice['bet_slug'];
                $palpite = $categoria['palpite'] === $choice['choice_slug'];
                return $mercado && $palpite;
            });

            $categoriaAntiga = $categoriasAntigas->first(function ($categoria) use ($choice) {
                return $categoria['name'] === $choice['bet_slug'];
            });

            if (is_null($categoria)) continue;

            if ($this->cotacaoExiste($choice)) continue;

            $valor = floatval($choice['value']);

            $cotacoes->push([
                'match_id' => $match->id,
                'bet_slug' => $choice['bet_slug'],
                'bet_name' => $choice['bet_name'],
                'value' => $valor,
                'choice_name' => $choice['choice_name'],
                'choice_slug' => $choice['choice_slug'],
                'bet_order' => $categoriaAntiga['order'] ?? 100,
            ]);
        }

        $match->quotations()->insert($cotacoes->toArray());

        $match->update([
            'quotations_qty' => $match->quotations()
                ->where('bet_slug', '!=', 'full_time_result')
                ->count()
        ]);
    }

    private function cotacaoExiste($choice)
    {
        $exists = $this->match->quotations()
                ->where('bet_slug', $choice['bet_slug'])
                ->where('choice_slug', $choice['choice_slug'])
                ->count() !== 0;

        if ($exists) $this->atualizarCotacao($choice);

        return $exists;
    }

    private function atualizarCotacao($choice)
    {
        $podeAtualizar = $this->match->quotations()
            ->where('bet_slug', $choice['bet_slug'])
            ->where('choice_slug', $choice['choice_slug'])
            ->first()
            ->upgradable;

        if ($podeAtualizar) {
            $choice['value'] = floatval($choice['value']);

            return $this->match->quotations()
                ->where('bet_slug', $choice['bet_slug'])
                ->where('choice_slug', $choice['choice_slug'])
                ->update($choice);
        }

        return true;
    }
}
