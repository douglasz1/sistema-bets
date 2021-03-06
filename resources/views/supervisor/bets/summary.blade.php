@extends('layouts.admin')

@section('content')
    <div class="block full">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Resumo da aposta</h2>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4>Código: <span class="text-muted">{{ $bet->id }}</span></h4>
                <h4>Apostador: <span class="text-muted">{{ $bet->client_name }}</span></h4>
                <h4>Situação:
                    <span class="text-muted hide-on-edit">
                        @if($bet->status === 'win')
                            <span class="text-success">Vencedor</span>
                        @elseif($bet->status === 'lose')
                            <span class="text-danger">Perdedor</span>
                        @elseif($bet->status === 'canceled')
                            <span class="text-warning">Cancelado</span>
                        @else
                            <span>Andamento</span>
                        @endif
                    </span>
                </h4>
                <h4>Operador: <span class="text-muted">{{ $bet->seller->name }}</span></h4>
            </div>
            <div class="col-md-6">
                <h4>Data: <span class="text-muted">{{ $bet->created_at->format('d/m/Y H:i') }}</span></h4>
                <h4>Valor: <span class="text-muted">R$ {{ moneyBR($bet->bet_value) }}</span></h4>
                <h4>Prêmio: <span class="text-muted">R$ {{ moneyBR($bet->prize) }}</span></h4>
                <h4>Comissão: <span class="text-muted">R$ {{ moneyBR($bet->commission) }}</span></h4>
                @if($bet->status !== 'canceled')
                    <a href="{{ route('supervisor.bets.cancel', ['id' => $bet->id]) }}"
                       class="btn btn-danger btn-effect-ripple">
                        <i class="fa fa-ban"></i>
                        Cancelar aposta
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="block full block-with-table">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Palpites da aposta</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-condensed table-hover">
                <thead>
                <tr>
                    <td><b>Partida</b></td>
                    <td><b>Liga</b></td>
                    <td><b>Data</b></td>
                    <td><b>Palpite</b></td>
                    <td><b>Cotação</b></td>
                    <td><b>Situação</b></td>
                    <td><b>1&deg; T</b></td>
                    <td><b>2&deg; T</b></td>
                    <td><b>R. Final</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                @forelse($bet->tips as $tip)
                    <tr>
                        <td>{{ ($tip->match) ? $tip->match->matchName() : $tip->match_id }}</td>
                        <td>
                            {{ ($tip->match) ? $tip->match->league->name : '' }}
                        </td>
                        <td>{{ ($tip->match) ? $tip->match->match_date->format('d/m/Y H:i') : '' }}</td>
                        <td>{{ $tip->choice_name }}</td>
                        <td>R$ {{ moneyBR($tip->value) }}</td>
                        <td>
                            @if($tip->status === 'win')
                                <span class="text-success">Vencedor</span>
                            @elseif($tip->status === 'lose')
                                <span class="text-danger">Perdedor</span>
                            @elseif($tip->status === 'canceled')
                                <span class="text-warning">Cancelado</span>
                            @else
                                <span>Andamento</span>
                            @endif
                        </td>
                        <td>{{ @$tip->match->home_1st . "-" . @$tip->match->away_1st }}</td>
                        <td>{{ @$tip->match->home_2nd . "-" . @$tip->match->away_2nd }}</td>
                        <td>{{ @$tip->match->home_final . "-" . @$tip->match->away_final }}</td>
                        <td>
                            @if($tip->status !== 'canceled')
                                <a href="{{ route('supervisor.tips.cancel', ['id' => $tip->id]) }}"
                                   class="btn btn-danger btn-sm btn-effect-ripple">
                                    <i class="fa fa-ban"></i>
                                    Cancelar
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">Nenhuma aposta encontrada</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
