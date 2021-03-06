@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Resultados</h2>
        </div>
        <table class="table table-vcenter table-condensed table-striped table-hover">
            @forelse($resultados->groupBy('match_id') as $partidas)
                <tr class="table-title">
                    @php
                        $p = $partidas->first()->match;
                    @endphp
                    <th colspan="5" class="text-center">
                        <h4>{{ $p->match_name }}</h4>
                        {{ "{$p->league->country->name}: {$p->league->name} | {$p->sport->name}" }}
                        <br>
                        {{ $p->match_date->format('d/m/Y H:i') }}
                    </th>
                </tr>
                <tr class="table-title">
                    <td><b>Mercado</b></td>
                    <td><b>Palpite</b></td>
                    <td style="width: 270px"><b>Opções</b></td>
                </tr>
                @foreach($partidas as $partida)
                    <tr>
                        <td>
                            {{ $partida->bet_name }}
                        </td>
                        <td>
                            {{ $partida->choice_name }}
                        </td>
                        <td>
                            <a href="{{ route('admin.results.salvar', ['evento' => $partida->match_id, 'mercado' => $partida->bet_slug, 'palpite' => $partida->choice_slug, 'status' => 'vencedor']) }}"
                               class="btn btn-success">
                                vencedor
                            </a>
                            <a href="{{ route('admin.results.salvar', ['evento' => $partida->match_id, 'mercado' => $partida->bet_slug, 'palpite' => $partida->choice_slug, 'status' => 'perdedor']) }}"
                               class="btn btn-danger">
                                perdedor
                            </a>
                            <a href="{{ route('admin.results.salvar', ['evento' => $partida->match_id, 'mercado' => $partida->bet_slug, 'palpite' => $partida->choice_slug, 'status' => 'cancelar']) }}"
                               class="btn btn-warning">
                                cancelar
                            </a>
                        </td>
                    </tr>

                    @if($loop->last)
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                    @endif
                @endforeach
            @empty
                <tr>
                    <td class="text-center">
                        <strong>Nenhum resultado encontrado</strong>
                    </td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection

@section('styles')
    <style>
        h4 {
            margin: 5px;
            padding: 0;
        }
    </style>
@endsection
