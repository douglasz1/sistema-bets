<?php

namespace Bets\Services\Bolao;


use Bets\Models\Bolao\Aposta;
use Bets\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ApostasService extends BaseService
{
    protected $modelClass = Aposta::class;

    public function paginar($bolaoId = null)
    {
        $query = $this->newQuery();

        if (!is_null($bolaoId)) {
            $query->where('bolao_id', $bolaoId);
        };

        $query->orderByDesc('created_at');

        $query->with('vendedor', 'palpites');

        return $this->doQuery($query, 30, true);
    }

    public function buscarSegundaVia($vendedorId)
    {
        $tempoParaImprimir = env('REPRINT_TIME', 0);

        if($tempoParaImprimir > 0) {
            return $query = $this->newQuery()
                ->where('situacao', 'pendente')
                ->where('vendedor_id', $vendedorId)
                ->whereRaw(DB::raw("NOW() <= DATE_ADD(`created_at`, INTERVAL {$tempoParaImprimir} MINUTE)"))
                ->orderBy('created_at', 'DESC')
                ->paginate();
        } else {
            return $query = $this->newQuery()
                ->where('vendedor_id', $vendedorId)
                ->orderBy('created_at', 'DESC')
                ->paginate();
        }
    }

    public function cancelar($apostaId)
    {
        $aposta = $this->update([
            'situacao' => 'cancelado',
            'cancelado_id' => auth()->id(),
        ], $apostaId);

        app(\Bets\Models\User::class)
            ->newQuery()
            ->where('id', $aposta->vendedor_id)
            ->increment('balance', $aposta->valor);

        return $aposta;
    }

    public function create(array $data)
    {
        $palpites = [];

        if (array_key_exists('palpite_simples', $data)) {
            $palpites = $this->palpitesSimples($data['palpite_simples'], $data['bolao_id']);
            unset($data['palpite_simples']);
        } else {
            for ($i = 0; $i < count($data['placar_casa']); $i++) {
                $palpites[] = [
                    'bolao_id' => $data['bolao_id'],
                    'palpite_casa' => $data['placar_casa'][$i],
                    'palpite_fora' => $data['placar_fora'][$i],
                ];
            }

            unset($data['placar_casa']);
            unset($data['placar_fora']);
        }

        $bolao = app(\Bets\Models\Bolao\Bolao::class)->find($data['bolao_id']);
        $data['valor'] = $bolao->valor;
        $data['comissao'] = $bolao->valor * ($bolao->vendedor / 100);

        $operador = auth()->user();
        $data['empresa_id'] = $operador->company_id;

        $aposta = parent::create($data);

        $aposta->palpites()->createMany($palpites);

        $bolao->increment('acumulado', $bolao->valor);

        $operador->decrement('balance', $bolao->valor);

        return $aposta;
    }

    private function palpitesSimples(array $dadosPalpites, $bolaoId)
    {
        $palpites = [];

        $palpites_simples = array_values($dadosPalpites);

        for ($i = 0; $i < count($palpites_simples); $i++) {
            $temp = [
                'palpite_casa' => 0,
                'palpite_fora' => 0,
                'bolao_id' => $bolaoId,
            ];

            if ($palpites_simples[$i] === '1') {
                $temp['palpite_casa'] = 1;

            } elseif ($palpites_simples[$i] === '2') {
                $temp['palpite_fora'] = 1;

            }

            $palpites[] = $temp;
        }

        return $palpites;
    }
}
