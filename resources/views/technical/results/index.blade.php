@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Opções de filtragem</h2>
        </div>
        {!! Form::open(['route' => 'technical.results.index', 'method' => 'get']) !!}
        <div class="btn-group btn-group-full btn-group-space p-tb p-lr">
            <div class="form-group">
                {!! Form::date('start_date', now(), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::date('end_date', now(), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::select('status', ['all' => 'Todos', 'pending' => 'Em adamento', 'finished' => 'Terminadas'], null, ['class' => 'form-control']) !!}
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fa fa-filter"></i> Filtrar
            </button>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="block full bordered">
        <div class="block-title">
            <div class="block-options pull-right">
                <button type="submit" form="save-scores" class="btn btn-success">
                    <i class="fa fa-save"></i> Salvar
                </button>
            </div>
            <h2>Cadastrar Resultados</h2>
        </div>
        {!! Form::open(['route' => 'technical.results.store', 'id' => 'save-scores']) !!}
        <div class="table-responsive">
            <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <td width="60px"><b>Data</b></td>
                    <td width="170px"><b>Liga</b></td>
                    <td><b>Partida</b></td>
                    <td width="90px"><b>1&deg; tempo</b></td>
                    <td width="90px"><b>2&deg; tempo</b></td>
                    <td width="90px"><b>Final</b></td>
                    <td colspan="2"><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                @forelse($results as $result)
                    <tr>
                        <td>{{ $result->match_date->format('d/m H:i') }}</td>
                        <td>
                            {{ $result->sport->name }}
                            <br>
                            {{ "{$result->league->country->name}: {$result->league->name}" }}
                        </td>
                        <td>
                            {{ $result->home_team }} <br>
                            {{ $result->away_team }}
                        </td>
                        <td>
                            @if($result->status === 'pending')
                                <div class="input-group">
                                    {!! Form::number('results[' . $result->id . '][home_1st]', null, ['class' => 'form-control']) !!}
                                    {!! Form::number('results[' . $result->id . '][away_1st]', null, ['class' => 'form-control']) !!}
                                </div>
                            @else
                                {{ $result->home_1st . ' x ' . $result->away_1st }}
                            @endif
                        </td>
                        <td>
                            @if($result->status === 'pending')
                                <div class="input-group">
                                    {!! Form::number('results[' . $result->id . '][home_2nd]', null, ['class' => 'form-control']) !!}
                                    {!! Form::number('results[' . $result->id . '][away_2nd]', null, ['class' => 'form-control']) !!}
                                </div>
                            @else
                                {{ $result->home_2nd . ' x ' . $result->away_2nd }}
                            @endif
                        </td>
                        <td>
                            @if($result->status === 'pending')
                                <div class="input-group">
                                    {!! Form::number('results[' . $result->id . '][home_final]', null, ['class' => 'form-control']) !!}
                                    {!! Form::number('results[' . $result->id . '][away_final]', null, ['class' => 'form-control']) !!}
                                </div>
                            @else
                                {{ $result->home_final . ' x ' . $result->away_final }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('technical.results.edit', ['id' => $result->id]) }}"
                               class="btn btn-info btn-effect-ripple">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('technical.results.cancel', ['id' => $result->id]) }}"
                               class="btn btn-danger btn-effect-ripple">
                                <i class="fa fa-times"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <strong>Nenhum resultado encontrado</strong>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('styles')
    <style>
        tbody tr > td:first-child {
            width: 60px !important;
            white-space: normal !important;
        }
        tbody tr > td:nth-child(4),
        tbody tr > td:nth-child(5),
        tbody tr > td:nth-child(6) {
            width: 90px !important;
            white-space: normal !important;
        }

        td .input-group input[type="number"] {
            width: 43px !important;
        }

        .table.table-vcenter th, .table.table-vcenter td {
            padding: 1px;
        }
    </style>
@endsection
