@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Bolão #{{ "{$bolao->id}: {$bolao->nome}" }} </h2>
        </div>
        <div class="btn-group btn-group-full btn-group-space p-tb p-lr">
            <a class="btn btn-primary" href="{{ route('technical.bolao.apostas', ['id' => $bolao->id]) }}">
                Apostas
            </a>
            <a class="btn btn-info" href="{{ route('technical.bolao.placares', ['id' => $bolao->id]) }}">
                Inserir placares
            </a>
            <a class="btn btn-success" href="{{ route('technical.bolao.vencedor', ['id' => $bolao->id]) }}">
                Calcular vencedor
            </a>
        </div>
        <div class="block-title">
            <h2>Detalhes: {{ $bolao->nome }}</h2>
        </div>
        <div class="row p-tb p-lr">
            <div class="col-md-6 text-light">
                <span class="text-muted">Apurado:</span>
                R$ {{ moneyBR($bolao->acumulado) }} (Até o momento)
                <br>
                <span class="text-muted">Valor do bolão:</span>
                R$ {{ moneyBR($bolao->valor) }}
                <hr>
                <span class="text-muted">Prêmio 1º lugar:</span>
                R$ {{ moneyBR(($bolao->premio_1 / 100) * $bolao->acumulado) }}
                <br>
                <span class="text-muted">Prêmio 2º lugar:</span>
                R$ {{ moneyBR(($bolao->premio_2 / 100) * $bolao->acumulado) }}
                <hr>
                <span class="text-muted">Banca:</span>
                R$ {{ moneyBR(($bolao->banca / 100) * $bolao->acumulado) }}
                <br>
                <span class="text-muted">Operadores ({{ $bolao->vendedor }}%):</span>
                R$ {{ moneyBR(($bolao->vendedor / 100) * $bolao->acumulado) }}
                <br>
                <span class="text-muted">Bônus operador:</span>
                R$ {{ moneyBR(($bolao->bonus_vendedor / 100) * $bolao->acumulado) }}
                <br>
                <span class="text-muted">Sistema:</span>
                R$ {{ moneyBR(($bolao->sistema / 100) * $bolao->acumulado) }}
                <hr>
                <span class="text-muted">Aposte até:</span>
                {{ dateToBrazil($bolao->data_limite) }}
                <br>
                <span class="text-muted">Data finalização:</span>
                {{ dateToBrazil($bolao->data_finalizar) }}
            </div>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-striped">
                        <thead>
                        <tr>
                            <td><b>#</b></td>
                            <td><b>Data</b></td>
                            <td class="text-right"><b>T. Casa</b></td>
                            <td width="50"></td>
                            <td width="50"></td>
                            <td><b>T. Fora</b></td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bolao->partidas as $partida)
                            <tr>
                                <td>#{{ $loop->index + 1 }}</td>
                                <td>{{ $partida->data_partida->format('d/m H:i') }}</td>
                                <td class="text-right">{{ $partida->time_casa }}</td>
                                <td class="text-right">
                                    {{ !is_null($partida->placar_casa) ? $partida->placar_casa : '-' }}
                                </td>
                                <td>
                                    {{ !is_null($partida->placar_fora) ? $partida->placar_fora : '-' }}
                                </td>
                                <td>{{ $partida->time_fora }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="block-title">
            <h2>1º Colocado(s) - Fizeram "{{ $bolao->pontuacao_1 }} pontos"</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-borderless table-striped table-vcenter table-condensed">
                <thead>
                <tr>
                    <td><b>Código</b></td>
                    <td><b>Cliente</b></td>
                    <td><b>Pontos</b></td>
                    <td><b>Vendedor</b></td>
                    <td><b>Data</b></td>
                    <td><b>Prêmio</b></td>
                </tr>
                </thead>
                <tbody>
                @foreach($bolao->apostas->where('total_pontos', $bolao->pontuacao_1) as $vencedor)
                    <tr>
                        <td>{{ $vencedor->id }}</td>
                        <td>{{ $vencedor->cliente }}</td>
                        <td>{{ $vencedor->total_pontos }}</td>
                        <td>{{ $vencedor->vendedor->name }}</td>
                        <td>{{ dateToBrazil($vencedor->created_at) }}</td>
                        <td>{{ $vencedor->total_pontos }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <hr>
        <div class="block-title m-t">
            <h2>2º Colocado(s) - Fizeram "{{ $bolao->pontuacao_2 }} pontos"</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-borderless table-striped table-vcenter table-condensed">
                <thead>
                <tr>
                    <td><b>Código</b></td>
                    <td><b>Cliente</b></td>
                    <td><b>Pontos</b></td>
                    <td><b>Vendedor</b></td>
                    <td><b>Data</b></td>
                    <td><b>Prêmio</b></td>
                </tr>
                </thead>
                <tbody>
                @foreach($bolao->apostas->where('total_pontos', $bolao->pontuacao_2) as $vencedor)
                    <tr>
                        <td>{{ $vencedor->id }}</td>
                        <td>{{ $vencedor->cliente }}</td>
                        <td>{{ $vencedor->total_pontos }}</td>
                        <td>{{ $vencedor->vendedor->name }}</td>
                        <td>{{ dateToBrazil($vencedor->created_at) }}</td>
                        <td>{{ $vencedor->total_pontos }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
