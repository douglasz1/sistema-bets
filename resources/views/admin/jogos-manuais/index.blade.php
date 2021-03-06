@extends('layouts.admin')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="block full bordered">
        <div class="block-title">
            <h2>Jogos manuais</h2>
        </div>

        <div class="block-content p-t">
            <div class="p-lr">
                <div class="form-group">
                    {!! Form::open(['route' => 'admin.jogos-manuais.index', 'id' => 'formEsportes']) !!}
                    {!! Form::label('esporte', 'Esporte', ['class' => 'control-label']) !!}
                    {!! Form::select('esporte', $esportes, Request::get('esporte', 1), ['class' => 'form-control']) !!}
                    {!! Form::close() !!}
                </div>

                <br>

                <label class="control-label">Adicionar campeonato</label>

                {!! Form::open(['route' => 'admin.jogos-manuais.salvar-liga']) !!}
                <input type="hidden" name="esporte" value="{{ Request::get('esporte', 1) }}">
                <div class="form-group">
                    {!! Form::text('nome_pais', null, ['class' => 'form-control', 'placeholder' => 'País']) !!}
                </div>

                <div class="form-group">
                    {!! Form::text('nome_campeonato', null, ['class' => 'form-control', 'placeholder' => 'Nome do campeonato']) !!}
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Adicionar novo
                        campeonato
                    </button>
                </div>
                {!! Form::close() !!}
            </div>

            <br>

            {!! Form::open(['route' => 'admin.jogos-manuais.salvar-evento']) !!}
            <input type="hidden" name="esporte" value="{{ Request::get('esporte', 1) }}">

            <div class="p-lr">
                <div class="form-group">
                    {!! Form::label('campeonato', 'Campeonato', ['class' => 'control-label']) !!}
                    {!! Form::select('campeonato', $ligas, Request::get('campeonato'), ['class' => 'form-control']) !!}
                </div>

                <br>

                <div class="form-group">
                    @if(Request::get('esporte', 1) == "7")
                        {!! Form::label('time_casa', 'Nome do evento', ['class' => 'control-label']) !!}
                    @else
                        {!! Form::label('time_casa', 'Time da Casa', ['class' => 'control-label']) !!}
                    @endif
                    {!! Form::text('time_casa', Request::get('time_casa'), ['class' => 'form-control', 'required']) !!}
                </div>

                @if(Request::get('esporte', 1) != "7")
                    <br>
                    <div class="form-group">
                        {!! Form::label('time_fora', 'Time de Fora', ['class' => 'control-label']) !!}
                        {!! Form::text('time_fora', Request::get('time_fora'), ['class' => 'form-control', 'required']) !!}
                    </div>
                @endif

                <br>

                <div class="form-group">
                    {!! Form::label('data', 'Data', ['class' => 'control-label']) !!}
                    {!! Form::text('data', Request::get('data'), ['class' => 'form-control date_time', 'required']) !!}
                </div>
            </div>

            <br>
            @if(Request::get('esporte', 1) == 1)
                @include('admin.jogos-manuais.tabela-futebol')
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter table-borderless table-striped table-hover">
                        <thead>
                        <tr>
                            <td style="width: 280px"><b>Aposta</b></td>
                            <td><b>Cotação</b></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><b>Nome do mercado:</b></td>
                            <td>
                                <input type="text" name="mercado" class="form-control" style="width:250px;"
                                       value="{{ Request::get('mercado') }}"
                                       placeholder="Mercado">
                            </td>
                        </tr>
                        @for($i = 0; $i < 12; $i++)
                            <tr>
                                <td>
                                    <input type="text" name="competidor[]" class="form-control" style="width:250px;"
                                           placeholder="Nome do competidor {{ $i + 1 }}">
                                </td>
                                <td>
                                    <input name="cotacao[]" type="text" class="form-control" style="width:250px;"
                                           placeholder="Cotação do competidor {{ $i + 1 }}">
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="p-lr p-tb">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Salvar Partida
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@section('styles')
    <style>
        .form-control {
            max-width: 300px;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.date_time').mask('99/99/9999 99:99', {
                placeholder: "__/__/____ __:__",
                clearIfNotMatch: true
            });

            $('#esporte').on('change', function () {
                document.getElementById("formEsportes").submit();
            });
        });
    </script>
@endsection
