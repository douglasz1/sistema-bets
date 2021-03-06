@extends('layouts.admin')

@section('content')
    {!! Form::open(['route' => ['technical.bolao.salvar']]) !!}
    <div class="block full bordered">
        <div class="block-title">
            <h2>Criar novo bolão</h2>
        </div>
        <div class="block-content form-horizontal form-bordered text-light">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('nome', 'Nome do bolão', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::text('nome', null, ['class' => 'form-control', 'required', 'placeholder' => 'Exemplo: Copa do Mundo']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('valor', 'Valor do bolão', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::select('valor', [
                                5 => 'R$ 5',
                                10 => 'R$ 10',
                                15 => 'R$ 15',
                                20 => 'R$ 20',
                                50 => 'R$ 50',
                            ], null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('tipo_bolao', 'Tipo do bolão', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::select('tipo_bolao', [
                                'simples' => 'Simples',
                                'completo' => 'Completo',
                            ], null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('inicial', 'Valor inicial para o Bolão (opcional):', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::number('inicial', null, ['class' => 'form-control', 'placeholder' => 'Inicie seu bolão com algum valor já apurado']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('imprimir_apurado', 'Imprimir apuração no bilhete?', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::select('imprimir_apurado', [
                                1 => 'Sim',
                                0 => 'Não',
                            ], 1, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block full bordered">
        <div class="block-title">
            <h2>Distribuição % do Bolão (padrão)</h2>
        </div>
        <div class="block-content form-horizontal form-bordered text-light">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('premio_1', '1&deg; Lugar', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::number('premio_1', 60, ['class' => 'form-control', 'required', 'placeholder' => 60, 'min' => 0, 'max' => 100]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('premio_2', '2&deg; Lugar', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::number('premio_2', 10, ['class' => 'form-control', 'required', 'placeholder' => 10, 'min' => 0, 'max' => 100]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('banca', 'Banca (Administrador)', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::number('banca', 18, ['class' => 'form-control', 'required', 'placeholder' => 18, 'min' => 0, 'max' => 100]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('vendedor', 'Operador', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::number('vendedor', 10, ['class' => 'form-control', 'required', 'placeholder' => 10, 'min' => 0, 'max' => 100]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('bonus_vendedor', 'Bônus Agente (Aposta em 1º lugar)', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::number('bonus_vendedor', 2, ['class' => 'form-control', 'required', 'placeholder' => 2, 'min' => 0, 'max' => 100]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('sistema', 'Sistema Bets', ["class" => "col-md-4 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::number('sistema', 0, ['class' => 'form-control', 'required', 'placeholder' => 0, 'min' => 0, 'max' => 100]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block full bordered">
        <div class="block-title">
            <h2>Jogos</h2>
        </div>
        <div class="block-content form-horizontal form-bordered text-light">
            @include('bolao.partidas._form')

            <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
                <a href="{{ route('technical.bolao.index') }}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Salvar
                </button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
