@extends('layouts.admin')

@section('content')
    <div class="block">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Editar nível de usuário</h2>
        </div>
        {!! Form::model($role, ['route' => ['admin.roles.update', 'id' => $role->id], 'class' => 'form-horizontal form-bordered']) !!}

        @include('admin.roles._form')

        <div class="form-group form-actions">
            <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-effect-ripple btn-primary"
                        style="overflow: hidden; position: relative;">
                    <i class="fa fa-save"></i> Salvar
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-effect-ripple btn-danger" style="overflow: hidden; position: relative;">
                    <i class="fa fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
@endsection
