@extends('layouts.admin')

@section('content')
    {!! Form::model($usuario, ['route' => 'atualizarSenha']) !!}

    <div class="block full bordered">
        <div class="block-title">
            <h2>Alterar senha</h2>
        </div>
        <div class="block-content form-horizontal form-bordered p-b p-lr">

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-key"></i></span>
                            {!! Form::password('old_password', ['class' => 'form-control', 'placeholder' => 'Senha atual']) !!}
                        </div>
                        @if ($errors->has('old_password'))
                            <div class="help-block animation-slideUp">{{ $errors->first('old_password') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-key"></i></span>
                            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Nova senha']) !!}
                        </div>
                        @if ($errors->has('password'))
                            <div class="help-block animation-slideUp">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-key"></i></span>
                            {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirmar nova senha']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-group btn-group-full btn-group-space p-t">
                <a href="{{ url('home') }}" class="btn btn-danger">
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
