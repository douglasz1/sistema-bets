@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Editar empresa</h2>
        </div>
        {!! Form::model($company, ['route' => ['admin.companies.update', 'id' => $company->id], 'class' => 'form-horizontal form-bordered text-light']) !!}

        @include('admin.companies._form')

        {!! Form::close() !!}
    </div>
@endsection
