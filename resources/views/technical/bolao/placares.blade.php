@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Bolão #{{ "{$bolao->id}: {$bolao->nome}" }} </h2>
        </div>
        <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
            <a class="btn btn-primary" href="{{ route('technical.bolao.detalhes', ['id' => $bolao->id]) }}">
                Detalhes
            </a>
            <a class="btn btn-info" href="{{ route('technical.bolao.apostas', ['id' => $bolao->id]) }}">
                Apostas
            </a>
            <a class="btn btn-success" href="{{ route('technical.bolao.vencedor', ['id' => $bolao->id]) }}">
                Calcular vencedor
            </a>
        </div>
        <div class="block-title">
            <h2>Placares: {{ $bolao->nome }}</h2>
        </div>
        {!! Form::model($bolao, ['route' => ['technical.bolao.placares.salvar', 'id' => $bolao->id]]) !!}
        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-striped table-borderless">
                <thead>
                <tr>
                    <td><b>#</b></td>
                    <td><b>Data</b></td>
                    <td class="text-right"><b>T. Casa</b></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td><b>T. Fora</b></td>
                </tr>
                </thead>
                <tbody>
                @foreach($bolao->partidas as $partida)
                    <tr>
                        <td>#{{ $loop->index + 1 }}</td>
                        <td>{{ dateToBrazil($partida->data_partida) }}</td>
                        <td class="text-right">{{ $partida->time_casa }}</td>
                        <td class="text-right">
                            {!! Form::number('placar_casa[]', $partida->placar_casa, ['min' => 0, 'class' => 'form-control', 'placeholder' => 'Placar casa']) !!}
                        </td>
                        <td>
                            {!! Form::number('placar_fora[]', $partida->placar_fora, ['min' => 0, 'class' => 'form-control', 'placeholder' => 'Placar fora']) !!}
                        </td>
                        <td>{{ $partida->time_fora }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="btn-group btn-group-full p-tb p-lr">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> Salvar
            </button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
