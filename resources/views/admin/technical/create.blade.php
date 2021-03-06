@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Criar técnico</h2>
        </div>
        {!! Form::model($technical, ['route' => 'admin.technical.store', 'class' => 'form-horizontal form-bordered text-light']) !!}

        @include('admin.technical._form')

        {!! Form::close() !!}
    </div>
@endsection
