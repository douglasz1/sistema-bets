@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h1 class="panel-title text-center">
                <strong>RESULTADO DO BOLÃO</strong> <br>
                Nº {{ "{$bolao->id} - {$bolao->nome}" }}</h1>
        </div>
        <div class="panel-body">
            <h2 class="text-center">
                <strong>Status:</strong> Finalizado.
                @if($bolao->imprimir_apurado)
                    <strong>Arrecadação:</strong> R$ {{ moneyBR($bolao->acumulado) }}
                @endif
            </h2>
            <div class="row">
                <h2 class="text-center">
                    <strong>PLACARES</strong>
                </h2>
                <div class="col-md-offset-1 col-xs-12">
                    @foreach($bolao->partidas as $partida)
                        <div class="col-md-1 col-xs-3 text-center">
                            <h3 class="pad0 mg0">j {{ $loop->index + 1 }}</h3>
                            <h2 class="pad0 mg0">
                                {{ "({$partida->placar_casa}×{$partida->placar_fora})" }}
                            </h2>
                            <small>Finalizado</small>
                        </div>
                    @endforeach
                </div>
            </div>
            <br>
            <p class="text-center">Apresente o bilhete impresso pelo terminal. Ele é o único comprovante para o
                recebimento do prêmio.</p>
            <h3>1º Colocado(s) fizeram "{{ $bolao->pontuacao_1 }} pontos" -
                R$ {{ moneyBR(($bolao->premio_1 / 100) * $bolao->acumulado) }}</h3>
            @php
                $quantidadeVencedor1 = $bolao->apostas->where('total_pontos', $bolao->pontuacao_1)->count();
                if ($quantidadeVencedor1 > 0) {
                    $premioPrimeiroLugar = moneyBR(($bolao->premio_1 / 100) * $bolao->acumulado / $quantidadeVencedor1);
                } else {
                    $premioPrimeiroLugar = 0;
                }
            @endphp
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Vendedor</th>
                    <th>Premiação</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bolao->apostas->where('total_pontos', $bolao->pontuacao_1) as $aposta)
                    <tr>
                        <td>{{ $aposta->id }}</td>
                        <td>{{ $aposta->vendedor->name }}</td>
                        <td>{{ $premioPrimeiroLugar }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pa10 pad0">
                <h2 class="pt12">Como Jogar</h2>
                <p class="pt9">Concorra ao nosso Bolão palpitando o resultado de 10 jogos. Ganha o(s) 1º e 2º
                    lugar com maior pontuação. O valor do bolão é de R$ {{ $bolao->valor }} (cada aposta).</p>
                <h3 class="pt12">Regras Gerais</h3>
                <p class="pt9">
                    * Em caso de empate na pontuação do Bolão a premiação será dividia por igual entre os
                    apostadores. <br>
                    * Em caso de jogo cancelado, a pontuação do jogo será de 0 (zero) pontos. <br>
                    * Em caso de jogo adiado para depois da data de finalização do bolão, a pontuação do jogo
                    será de 0 (zero) pontos.
                </p>
                <h3 class="pt12">Regras da Pontuação</h3>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="pad5">Regra</th>
                        <th class="tr">Pontuação</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($bolao->tipo_bolao === 'completo')
                        <tr class="hide">
                            <td class="pt9">
                                Acertar Placar Exato da partida.
                            </td>
                            <td class="tr pt9">
                                20 Pontos
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="pt9">
                            Acertar Placar Quebrado (casa, empate ou fora), mas, errar o placar exato.
                        </td>
                        <td class="tr pt9">
                            10 Pontos
                        </td>
                    </tr>
                    <tr>
                        <td class="pt9">
                            Não acertar nenhuma das opções acima.
                        </td>
                        <td class="tr pt9">
                            0 Pontos
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
