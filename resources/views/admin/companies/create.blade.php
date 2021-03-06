@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Cadastrar empresa</h2>
        </div>
        {!! Form::model($company, ['route' => ['admin.companies.store'], 'class' => 'form-horizontal form-bordered text-light']) !!}

        @include('admin.companies._form')

        {!! Form::close() !!}
    </div>
@endsection
