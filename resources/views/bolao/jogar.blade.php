@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Bolão #{{ "{$bolao->id}: {$bolao->nome}" }}</h2>
        </div>
        <div class="row text-light p-lr p-tb">
            <div class="col-md-6" style="font-weight: bold">
                <span class="text-muted">Prêmio 1º lugar:</span>
                R$ {{ moneyBR(($bolao->premio_1 / 100) * $bolao->acumulado) }}
                <span class="text-muted">(Até o momento)</span>
                <br>
                <span class="text-muted">Prêmio 2º lugar:</span>
                R$ {{ moneyBR(($bolao->premio_2 / 100) * $bolao->acumulado) }}
                <span class="text-muted">(Até o momento)</span>
                <br>
            </div>
            <div class="col-md-6">
                <span class="text-muted">Data finalização:</span>
                {{ dateToBrazil($bolao->data_finalizar) }}
                <br>
                <span class="text-muted">Aposte até:</span>
                {{ dateToBrazil($bolao->data_limite) }}
            </div>
        </div>
        <div class="block-title">
            <h2>Partidas</h2>
        </div>
        {!! Form::open(['route' => 'bolao.salvar']) !!}
        {!! Form::hidden('bolao_id', $bolao->id) !!}
        @if($bolao->tipo_bolao === 'completo')
            <table class="table table-vcenter table-borderless table-striped table-hover">
                @foreach($bolao->partidas as $partida)
                    <tr>
                        <td>j{{ $loop->index + 1 }}</td>
                        <td class="text-right">
                            <label>{{ $partida->time_casa }}</label>
                        </td>
                        <td>
                            <input placeholder="" class="form-control" type="number" name="placar_casa[]" min="0"
                                   max="10" required="required">
                        </td>
                        <td class="text-center">x</td>
                        <td>
                            <input placeholder="" class="form-control" type="number" name="placar_fora[]" min="0"
                                   max="10" required="required">
                        </td>
                        <td>
                            <label>{{ $partida->time_fora }}</label>
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            <table class="table table-borderless table-vcenter table-striped table-hover table-condensed">
                @foreach($bolao->partidas as $partida)
                    <tr>
                        <td>
                            <h4>{{ "{$partida->time_casa} x {$partida->time_fora}" }}</h4>
                            <h5 class="text-muted">{{ dateToBrazil($partida->data_partida) }}</h5>
                        </td>
                        <td>
                            <div class="btn-group btn-group-full btn-group-space" data-toggle="buttons">
                                <label class="btn btn-default">
                                    <input required type="radio" name="palpite_simples[{{ $partida->id }}]" value="1">
                                    <span>Casa</span>
                                </label>
                                <label class="btn btn-default">
                                    <input required type="radio" name="palpite_simples[{{ $partida->id }}]" value="X">
                                    <span>Empate</span>
                                </label>
                                <label class="btn btn-default">
                                    <input required type="radio" name="palpite_simples[{{ $partida->id }}]" value="2">
                                    <span>Fora</span>
                                </label>
                            </div>
                        </td>
                    </tr>

                @endforeach
            </table>
        @endif
        <div class="block-content form-horizontal form-bordered">
            <div class="btn-group btn-group-full btn-group-space p-lr">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">R$</span>
                        <input readonly type="text" class="form-control" value="{{ moneyBR($bolao->valor) }}">
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::text('cliente', null, ['class' => 'form-control', 'placeholder' => 'Cliente', 'required']) !!}
                </div>
            </div>
            <div class="btn-group btn-group-full btn-group-space p-lr p-b">
                <a href="{{ route('bolao.index') }}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Salvar
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('styles')
    <style>
        .table-striped > tbody > tr {
            color: #000;
            background-color: #FFFFFF !important;
            border-bottom: solid 1px #d3d3d3;
        }

        .table-hover > tbody > tr:hover > td,
        .table-hover > tbody > tr:hover > th {
            background-color: #dddddd !important;
        }

        .table .btn-group-full {
            flex-wrap: nowrap;
        }
        .table .btn-group-full .btn {
            flex-basis: 30% !important;
        }

        .table label.btn:focus,
        .table label.btn.focus,
        .table label.btn.active {
            outline: none;
            color: #FFFFFF;
            background-color: darkred;
            border-color: #5d0000;
        }
    </style>
@endsection
